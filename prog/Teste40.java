import java.io.*;
import java.util.*;
import java.lang.reflect.*;

public abstract class Teste40 {

	public static void main(String[] args) throws Exception {
		try {
			System.out.println(new File("rt.jar").length());
			System.out.println(new File("rt2.jar").length());
		} catch (Exception e) {
			System.out.println(e);
		}
	}
}