import java.io.*;
import java.util.*;
import java.net.*;
import java.lang.reflect.*;

public class Teste55_TestClass implements Teste55_LocalModule {
	
	private static int count = 0;
	
	static {
		System.out.println("Now initializing static class for " + Teste55_TestClass.class.getName() + '.');
	}

	/** This is our start function */
	public void start(String opt) {
		System.out.println("Running the Test class, option was '"+opt+"' ("+(++count)+")");
		System.out.println("Now initializing a test object.");
		System.out.println("DONE");
		//System.out.println("ERROR!!!!");
	}
}
