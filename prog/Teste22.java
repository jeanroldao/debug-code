import java.util.*;
import java.io.*;
import java.sql.*;

/*
C:\Users\jean_roldao\Downloads\debug-code-master\prog>javac -d jout -XD-printflat Teste22.java
*/

enum Teste22_Coisa {COPO, PRATO, GARFO, FACA, COLHER}

public class Teste22 {
	
	
	public static void main2(String[] args) throws Exception {
		//System.out.println(getClassTest().getClassLoader());
		//System.out.println(Teste22.class.getClassLoader().loadClass("Teste22$1"));
		//System.out.println(Teste22_Coisa.PRATO);
	}
	
	public static Class<?> getClassTest() throws Exception {
		return Class.forName("Teste22$1");
	}
	
	public static void main(String[] args) throws Exception {
		
		Teste22_Coisa n = Teste22_Coisa.valueOf("COLHER");
		
		
		switch (n) {
			//case 0:
				//System.out.println(0);
			case COPO:
				System.out.println("COPO");
				System.out.println("break");
				break;
			case PRATO:
				System.out.println("PRATO");
			case GARFO:
				System.out.println("GARFO");
			case FACA:
				System.out.println("FACA");
			default:
				System.out.println("default");
		}
		System.out.println("end");
	}
	
	public static void main1(String[] args) throws Exception {
		Properties p = new Properties();
		p.setProperty("name", "nana");
		
		p.toString();
		System.out.println(p);
	}
	
	public static void main0(String[] args) throws Exception {
		
		Connection connection = null;  
        ResultSet resultSet = null;  
        Statement statement = null;  
  
        try {  
            Class.forName("smallsql.database.SSDriver");  
            connection = DriverManager  
                    .getConnection("jdbc:smallsql:C:\\SmallSQL\\database\\EMPLOYEEDB");  
            statement = connection.createStatement();  
            resultSet = statement  
                    .executeQuery("SELECT EMPNAME FROM EMPLOYEEDETAILS");  
            while (resultSet.next()) {  
                System.out.println("EMPLOYEE NAME:"  
                        + resultSet.getString("EMPNAME"));  
            }  
        } catch (Exception e) {  
            e.printStackTrace();  
			throw e;
		}
	}
	
}

