
public class PHP {
	
	static {
		try {
			System.loadLibrary("PHPlib");
		} catch (UnsatisfiedLinkError e) {
			System.out.println("PHPlib not found!");
			System.exit(0);
		}
	}
	
	public static native void exec(String func, Object... args);
}