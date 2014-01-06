import java.util.*;
import java.io.*;
import java.sql.*;
import java.net.*;

public class TesteSmallSql {

	private static void var_dump(Object var) {
		try {
			var_dump0(var);
		} catch (UnsatisfiedLinkError e) {
			System.out.println(var);
		}
	}
	private static native void var_dump0(Object var);
	
	public static void main(String[] args) throws Exception {
		try {
			main0(args);
		} catch (Exception e) {
			System.out.println("SmallSql error!!!");
			System.out.println(e);
		}
	}
	public static void main0(String[] args) throws Exception {
		//java.sql.DriverManager.setLogStream(System.out);
		System.out.println("SmallSql test");
		System.out.println(System.getProperty("jdbc.drivers"));
		
		/*
		Enumeration<URL> u = ClassLoader.getSystemClassLoader().getResources("META-INF/services/java.sql.Driver");
		//String strcls = "jar:file:/C:/bea/bea_10.3.5/jrockit_160_24_D1.1.2-4/jre/lib/rt.jar!/java/lang/String.class";
		//URL u = new URL(strcls);
		System.out.println(u);
		//var_dump(u);
		while (u.hasMoreElements()) {
			System.out.println(u.nextElement());
		}
		System.out.println("END");
		//*/
		
		///*
		//Class<?> cls = Class.forName("smallsql.database.SSDriver");
		//Class<?> cls = Class.forName("java.util.concurrent.locks.AbstractQueuedSynchronizer$Node");
		Class<?> cls = Class.forName("smallsql.database.ExpressionValue");
		System.out.println(cls);
		//cls.newInstance();
		
		/*
		Enumeration<Driver> drivers = DriverManager.getDrivers();
		System.out.println("-DRIVERS-START-");
		while (drivers.hasMoreElements()) {
			System.out.println(drivers.nextElement());
		}
		System.out.println("-DRIVERS-END-");
		
		//File file = new File("IE.php");
		//java.lang.reflect.Field field = File.class.getDeclaredField("fs");
		//field.setAccessible(true);
		//System.out.println(field.get(file));
		
		//C:\Program Files\EasyPHP-12.1\www\debug\prog\emp1
		String db = "emp1";
		Connection con = DriverManager.getConnection( "jdbc:smallsql:"+db );
		
		System.out.println(con);
		
		Statement statement = con.createStatement();
		//ResultSet result = statement.executeQuery("SELECT * FROM MESSAGES");  
		ResultSet result = statement.executeQuery("select 'jean' as user, 'oi' as message");  
		System.out.println("-MESSAGES-START-");
		while (result.next()) {  
			System.out.print(result.getString("user"));  
			System.out.print(": ");  
			System.out.println(result.getString("message"));  
		}  
		System.out.println("-MESSAGES-END-");
		//*/
	}
	
	private static String URLtoString2(URL u) {
		return u.toString();
	}
	private static String URLtoString(URL u) {
		StringBuffer result = new StringBuffer();
        result.append(u.getProtocol());
        result.append(":");
        if (u.getAuthority() != null && u.getAuthority().length() > 0) {
            result.append("//");
            result.append(u.getAuthority());
        }
        if (u.getPath() != null) {
            result.append(u.getPath());
        }
        if (u.getQuery() != null) {
            result.append('?');
            result.append(u.getQuery());
        }
        if (u.getRef() != null) {
            result.append("#");
            result.append(u.getRef());
        }
        return result.toString();
	}
	
}

