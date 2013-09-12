import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.util.Properties;

public class proptest {
    public static void main(String[] args) 
		throws Exception {
		//FileOutputStream propFile = new FileOutputStream( "myProperties.txt");
		System.getProperties().storeToXML(System.out, "nao sei comentar");
	}
	public static void main1(String[] args)
        throws Exception {

        // set up new properties object
        // from file "myProperties.txt"
        FileInputStream propFile =
            new FileInputStream( "myProperties.txt");
        Properties p =
            new Properties(System.getProperties());
        p.load(propFile);

        // set the system properties
        System.setProperties(p);
        // display new properties
        System.getProperties().list(System.out);
    }
}