# Frontend observability plan: React/TypeScript SPA + Symfony backend

## Goal

Every uncaught browser error, failed API call, and React render crash appears in the
backend's Monolog stream, with enough context to reproduce it and join it to the backend
request that caused it. No paid error tracker. Effort is roughly two developer days.

The core idea: expose one small endpoint in Symfony that accepts a log record from the
browser and hands it to the normal `LoggerInterface`. Browser errors then land in Monolog
next to backend logs, and flow through whatever handlers and shipping already exist, at
zero extra cost.

## Principles

- The backend is the only sink. The frontend never talks to a third party.
- Errors and warnings are always on in production. Anything richer is sampled or behind a flag.
- Logs never contain tokens or personal data. This is enforced server side, not trusted to the client.

## Phase 1: backend ingest endpoint (half a day)

### 1.1 Choose the endpoint implementation

Two options:

**`nelmio/js-logger-bundle`.** Works on Symfony 7. Install it and import its route, and you
are done in 15 minutes. It is a single controller at `/log` accepting `GET` (tracking-pixel
style, query parameters) and `POST` with a JSON body:

```json
{
  "level": "error",
  "msg": "Cannot read properties of undefined (reading 'id')",
  "context": { "file": "https://app.example/assets/main.js", "line": 1, "column": 4821 },
  "stack": [ { "fileName": "...", "lineNumber": 1, "columnNumber": 4821, "functionName": "..." } ]
}
```

The controller passes `level`, `msg` and `context` to the `logger` service after checking
`allowed_levels`, `ignore_messages` and `ignore_url_prefixes` from its configuration. Its
Twig helpers (`nelmio_js_error_logger()`, `nelmio_js_logger()`) are useless for a React SPA,
so you get only the controller.

Weaknesses: no payload validation, no size limit, no rate limit, the `GET` route is also
open, and it emits a PHP warning on a `POST` without a `stack` array.

**Own controller (recommended).** About 60 lines modelled on the bundle. Same JSON contract,
but you own validation and hardening. The bundle is still worth reading as the reference.

### 1.2 Dedicated Monolog channel

Log frontend records to a channel named `frontend`. It flows into the same handlers as
everything else, but can be filtered, routed to its own file, or muted independently:

```yaml
# config/packages/monolog.yaml
monolog:
  channels: ['frontend']
  handlers:
    main:
      type: stream
      path: php://stderr
      formatter: monolog.formatter.json
```

Inject the channel into the controller with `#[Autowire(service: 'monolog.logger.frontend')]`
or the `monolog.logger` tag with `channel: frontend`.

### 1.3 Server-side enrichment

Add user id, IP, user agent, backend request id and app version to the record via a Monolog
processor. Do not accept these from the client.

### 1.4 Hardening (all mandatory)

- **Allowed levels:** `warning`, `error`, `critical` only. Anything else returns 400. This
  stops anonymous callers from paging you at `alert` or `emergency` level.
- **Body size cap** of about 16 KB. Truncate long strings.
- **Rate limit per IP** with `symfony/rate-limiter`, for example 30 per minute. The endpoint
  stays anonymous because login-page errors matter most.
- **Scrub** `Authorization` headers, `eyJ…` JWT-shaped strings and e-mail addresses from the
  message and stack before logging.
- **Ignore lists** for known browser noise. Start with `ResizeObserver loop`, `Script error.`,
  and anything whose file starts with `chrome-extension://` or `moz-extension://`.
- **Method:** `POST` only. Do not expose the `GET` pixel variant.

## Phase 2: frontend reporting module (half a day)

A single `telemetry.ts` with no dependencies, about 150 lines, exporting one
`report(level, message, context)` function. Wire it up in four places:

1. `window.onerror` and `window.onunhandledrejection`.
2. A React `ErrorBoundary` at the app root and around each route. Report the component
   stack as context, then render the fallback.
3. The HTTP client's error interceptor: report status, method, URL path without query string,
   and the `X-Request-Id` from the response header. Network failures and 5xx at `error`,
   4xx at `warning`.
4. Explicit `report()` calls in `catch` blocks that swallow errors today.

Behaviour rules inside the module:

- **Client-side context:** build hash, current route, a random per-tab session id, viewport,
  and the last known backend request id.
- **Dedupe** by message plus first stack line per session, and **cap** at about 20 reports
  per minute. A render loop must not produce 10,000 requests.
- **Send** with `fetch` and `keepalive: true` so reports survive page unload.
- Use the native `Error.stack`. Do not load stacktrace.js from a CDN as the bundle does.
- Add `crossorigin` to any cross-origin `<script>` tag, otherwise the browser reports only
  `Script error.` with no file or line.
- In development, log to the console instead of the endpoint.

## Phase 3: correlation (two hours)

- The backend sets `X-Request-Id` on every response and includes it in every log line via a
  Monolog processor. Generate one if the client did not send it.
- The frontend generates a request id per API call, sends it as a header, and includes it in
  any error report about that call. A `500` seen in the browser then joins to the backend
  exception log in one query.

## Phase 4: source maps (two hours)

Production bundles are minified, so raw stacks are unreadable. Emit hidden source maps at
build time (`build.sourcemap: 'hidden'` in Vite) and keep them out of the public web root.
Store them per build hash, then resolve stacks offline with a small script using the
`source-map` npm package. Resolving at ingest time is possible later but not needed on day one.

## Phase 5: using the logs (two hours)

Frontend events are ordinary Monolog JSON records with `channel: frontend`, so whatever
already ships backend logs (stdout to journald, Docker, CloudWatch, Loki, ELK) picks them up
unchanged. There is nothing new to deploy or pay for. What changes is how you read them.

### Day-to-day queries

Filter on the channel first, then group by message. With plain files or `docker logs`:

```bash
# last 200 frontend errors
docker logs app 2>&1 | jq -c 'select(.channel == "frontend" and .level_name == "ERROR")' | tail -200

# top messages today
docker logs --since 24h app 2>&1 \
  | jq -r 'select(.channel == "frontend") | .message' | sort | uniq -c | sort -rn | head -20

# everything for one backend request id, browser and server side together
docker logs app 2>&1 | jq -c 'select(.extra.request_id == "a1b2c3")'
```

The same three queries in Loki / LogQL:

```logql
{app="backend"} | json | channel="frontend" | level_name="ERROR"
sum by (message) (count_over_time({app="backend"} | json | channel="frontend" [24h]))
{app="backend"} | json | extra_request_id="a1b2c3"
```

And in CloudWatch Logs Insights:

```
fields @timestamp, message, context.route, context.build, extra.user_id
| filter channel = "frontend" and level_name = "ERROR"
| sort @timestamp desc | limit 200

filter channel = "frontend" | stats count() by message | sort count desc
```

### Triage routine

- **After every deploy:** compare the top-messages query for the last hour against the same
  query for the previous seven days, grouped by `context.build`. A message that exists only
  under the new build hash is a regression from that deploy.
- **Weekly:** review the top 20 messages. Each is either a bug to ticket, or noise to add to
  the server-side ignore list. Do this for the first month, then monthly.
- **Investigating one report:** take the backend request id from the frontend record, query
  for it, and read the backend exception next to the browser error. Take the build hash and
  minified stack, run the offline source-map script, and you have the original file and line.

### Alerting

One rule is enough to start: frontend `ERROR` count in the last hour exceeds three times the
hourly median of the previous seven days, or a message fingerprint appears that was absent in
the previous seven days. Route it to the same place backend alerts go. Do not alert on
`warning`.

Once the routine is stable, write down the queries and the current noise list next to the
code, so the next person does not rediscover them.

## Optional cheap extras once the basics work

- Web Vitals via the 2 KB `web-vitals` package, sent at `info` level with 10 percent sampling.
- A `page_view` event so you have a denominator and can compute error rate per view.
- If you later want grouping UI, issue lifecycle and release tracking, self-hosted GlitchTip
  speaks the Sentry protocol and can sit behind the same endpoint. Do not start there.

## Verification checklist

1. Throw an error from a button handler. One `frontend` channel line appears with stack,
   route, user id and build hash.
2. Reject a promise without a handler. Same result.
3. Force a backend 500. The browser report carries the same request id as the backend
   exception line.
4. Loop `curl` against the endpoint. Requests are rejected after the limit.
5. Post a fake JWT in the message. The stored record shows it redacted.
6. Post `level: alert`. Response is 400.
