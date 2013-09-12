import java.util.*;
import java.io.*;

public class Teste4 {

	public static void main(String[] args) throws Exception {
	
		// create a vector of strings
		Vector<String> strings = new Vector<String>(10);

		// cast the vector to a generic vector
		Vector objects = strings;

		// insert an object into the vector
		objects.add(new Object());

		// fetch an object from the vector of strings
		Object anObject = strings.get(0);

		// fetch a string from the vector of strings
		String aString = strings.get(0);//jvm = ClassCastException, php = Object inside String variable
		System.out.println(aString instanceof String);
	}
	
}

