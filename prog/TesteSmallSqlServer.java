import java.util.*;
import java.io.*;
import java.sql.*;
import java.net.*;

import java.net.InetSocketAddress;

import com.sun.net.httpserver.HttpExchange;
import com.sun.net.httpserver.HttpHandler;
import com.sun.net.httpserver.HttpServer;

public class TesteSmallSqlServer implements HttpHandler {

	private int c = 0;
	
	private Connection conn;
	
	private List<Message> messages_db;
	
	public static void main(String[] args) throws Exception {
		System.out.println("starting server...");
		HttpServer server = HttpServer.create(new InetSocketAddress(8123), 0);
		server.createContext("/test", new TesteSmallSqlServer());
		server.setExecutor(null); // creates a default executor
		server.start();
		System.out.println("server started");
    }
	
	public TesteSmallSqlServer() {
		try {
			//Class.forName("smallsql.database.SSDriver");
			//conn = DriverManager.getConnection("jdbc:smallsql:emp1");
			messages_db = new ArrayList<Message>();
		} catch (Exception e) {
			System.out.println(e);
		}
	}
	
	private List<Message> getMessages0() {
		try {
			Statement statement = conn.createStatement();
			ResultSet result = statement.executeQuery("SELECT * FROM messages");
			ArrayList<Message> messages = new ArrayList<Message>();
			
			while (result.next()) {  
				messages.add(new Message(result.getString("user"), result.getString("message")));
			}  
			return messages;
		} catch (Exception e) {
			System.out.println(e);
			return new ArrayList<Message>();
		}
	}
	
	private List<Message> getMessages() {
		return messages_db;
	}
	
	private void saveMessage0(Message message) {
		try {
			PreparedStatement statement = conn.prepareStatement("INSERT INTO messages (user, message) values (?, ?)");
			statement.setString(1, message.getUser());
			statement.setString(2, message.getMessage());
			statement.executeUpdate();				
		} catch (Exception e) {
			System.out.println(e);
		}
	}
	
	private void saveMessage(Message message) {
		messages_db.add(message);
	}
	
	public void handle(HttpExchange t) throws IOException {
		System.out.println("request open");
		
		saveMessage(new Message(System.getProperty("os.name"), System.currentTimeMillis() + " currentTimeMillis"));
		List<Message> messages = getMessages();
		
		StringBuilder sb = new StringBuilder();
		sb.append("This is the response #");
		sb.append(c++);
		sb.append("\n(");
		sb.append(messages.size());
		sb.append(" messages)\n");
		
		for (Message m : messages.toArray(new Message[0])) {
			sb.append("{user: ");
			sb.append(m.getUser());
			sb.append(", message: ");
			sb.append(m.getMessage());
			sb.append("}\n");
		}
		byte[] response = sb.toString().getBytes();
		t.sendResponseHeaders(200, response.length);
		OutputStream os = t.getResponseBody();
		os.write(response);
		os.close();
		
		System.out.println("request close");
	}
	
	static class Message {
		String user;
		String message;
		
		public Message(String user, String message) {
			this.user = user;
			this.message = message;
		}
		public String getUser() {
			return user;
		}
		public String getMessage() {
			return message;
		}
	}
	
	
}

