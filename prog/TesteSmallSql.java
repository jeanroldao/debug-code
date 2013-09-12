import java.util.*;
import java.io.*;
import java.sql.*;

public class TesteSmallSql {
	
	public static void main(String[] args) throws Exception {
		System.out.println("SmallSql test");
		
		Class.forName("smallsql.server.SSDriver");
		
		Connection con = DriverManager.getConnection( "jdbc:smallsql:db1" );
		
		System.out.println(con);
		
	}
	
}

