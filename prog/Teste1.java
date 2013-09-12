import java.util.*;

public class Teste1 {

	public static final boolean Debug = true;
	
	public static boolean Testing = false;
	
	public static boolean getDebug() {
		return Debug;
	}
	
	public String getInstanceDebug() {
		return Debug ? "com debug" : "sem debug";
	}
	
	public void setInstanceTesting(boolean test) {
		Testing = test;
	}
	
	public String getInstanceTesting() {
		return Testing ? "com testes" : "sem testes";
	}
}