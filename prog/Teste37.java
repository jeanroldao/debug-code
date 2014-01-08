import java.io.*;
import java.util.*;
import java.lang.reflect.*;

public abstract class Teste37 {

	public static void main(String[] args) throws Exception {
		Class<?> cls = Teste37.class;
		System.out.println(cls.getClassLoader());
		System.out.println(cls.getResource("rt.jar"));
		
	}
	
}

