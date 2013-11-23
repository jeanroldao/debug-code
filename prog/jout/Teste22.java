
import java.util.*;
import java.io.*;
import java.sql.*;

public class Teste22 {
    {
    }
    
    public Teste22() {
        super();
    }
    
    public static void main2(String[] args) throws Exception {
    }
    
    public static Class getClassTest() throws Exception {
        return Class.forName("Teste22$1");
    }
    
    public static void main(String[] args) throws Exception {
        Teste22_Coisa n = Teste22_Coisa.valueOf("COLHER");
        switch (Teste22$1.$SwitchMap$Teste22_Coisa[n.ordinal()]) {
        case 1: 
            System.out.println("COPO");
            System.out.println("break");
            break;
        
        case 2: 
            System.out.println("PRATO");
        
        case 3: 
            System.out.println("GARFO");
        
        case 4: 
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
            connection = DriverManager.getConnection("jdbc:smallsql:C:\\SmallSQL\\database\\EMPLOYEEDB");
            statement = connection.createStatement();
            resultSet = statement.executeQuery("SELECT EMPNAME FROM EMPLOYEEDETAILS");
            while (resultSet.next()) {
                System.out.println("EMPLOYEE NAME:" + resultSet.getString("EMPNAME"));
            }
        } catch (Exception e) {
            e.printStackTrace();
            throw e;
        }
    }
}
