
public class DerbyTest {
    private String framework = "embedded";
    private String driver = "org.apache.derby.jdbc.EmbeddedDriver";
    private String protocol = "jdbc:derby:";
	
    public static void main(String[] args)
    {
        new DerbyTest().go(args);
        System.out.println("DerbyTest finished");
    }
}