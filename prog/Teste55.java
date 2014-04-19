import java.io.*;
import java.util.*;
import java.net.*;
import java.lang.reflect.*;

public class Teste55 {
	
	public static void main(String[] args) throws Exception {
		ClassLoader cl = SimpleClassLoader.class.getClassLoader();
		SimpleClassLoader sc = new SimpleClassLoader(cl); 
		String tst = "Teste55_TestClass"; 
		System.out.println("This program will use SimpleClassLoader."); 
		
		try {
			//Teste55_LocalModule o = (Teste55_LocalModule) sc.loadClass(tst).newInstance();
			//Thread.currentThread().setContextClassLoader(sc);
			Teste55_LocalModule o = ((Class<Teste55_LocalModule>)Class.forName(tst)).newInstance();
			o.start("none"); 
			System.out.println(o.getClass());
			System.out.println(o.getClass().getClassLoader());
			
			//new Scanner(System.in).nextLine();
			Teste55_LocalModule o1 = (Teste55_LocalModule) sc.loadClass(tst).newInstance(); 
			o.start("super"); 
			System.out.println(o.getClass());
			System.out.println(o.getClass().getClassLoader());
		} catch (NullPointerException e) { 
			System.out.println("Caught exception : "+e); 
		}
	}
	
	public static void main0(String[] args) throws Exception {
		System.out.println("class loader test");
		Random r = new Random();
		System.out.println(r.nextInt());
		System.out.println(r.nextInt());
	}
	
	public static class SimpleClassLoader extends ClassLoader {
		
		public SimpleClassLoader(ClassLoader parent) {
			super(parent);
		}

		public Class loadClass(String name) throws ClassNotFoundException {
			if(!"Teste55_TestClass".equals(name)) {
				return super.loadClass(name);
			}

			try {
				InputStream input = getResourceAsStream("Teste55_TestClass.class");
				//System.out.println(input);
				ByteArrayOutputStream buffer = new ByteArrayOutputStream();
				int data = input.read();

				while(data != -1){
					buffer.write(data);
					data = input.read();
				}

				input.close();

				byte[] classData = buffer.toByteArray();

				return defineClass("Teste55_TestClass", classData, 0, classData.length);

			} catch (MalformedURLException e) {
				e.printStackTrace();
			} catch (IOException e) {
				e.printStackTrace(); 
			}

			return null;
		}	
	}
	

}	
