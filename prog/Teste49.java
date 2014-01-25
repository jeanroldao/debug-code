import java.io.*;
import java.util.*;
import java.lang.reflect.*;

import java.net.InetSocketAddress;

import java.util.concurrent.Executors;
import java.util.concurrent.ExecutorService;

import com.sun.net.httpserver.HttpExchange;
import com.sun.net.httpserver.HttpHandler;
import com.sun.net.httpserver.HttpServer;

public abstract class Teste49 {
	public static void main0(String[] args) throws Exception {
		System.out.println(System.getProperty("com.sun.net.httpserver.HttpServerProvider"));
	}
	public static void main(String[] args) throws Exception {
		System.out.println("starting server...");
		HttpServer server = HttpServer.create(new InetSocketAddress(8123), 0);
		server.createContext("/test", new MyHandler());
		server.setExecutor(null); // creates a default executor
		server.start();
		System.out.println("server started");
    }

    static class MyHandler implements HttpHandler {
		private int c = 0;
        public void handle(HttpExchange t) throws IOException {
			System.out.println("request open");
			
			byte[] response = ("This is the response #" + (c++)).getBytes();
			t.sendResponseHeaders(200, response.length);
			OutputStream os = t.getResponseBody();
			os.write(response);
			os.close();
			
			System.out.println("request close");
        }
    }
	
}