import java.io.*;
import java.util.*;
import java.lang.reflect.*;
import org.apache.pdfbox.util.PDFTextStripper;
import org.apache.pdfbox.pdmodel.PDDocument;

public abstract class Teste50 {
	
	static class T1 {
		private String name;
		public T1(String name) {
			this.name = name;
		}
		public String toString() {
			return "{name=" + name + "}";
		}
	}
	
	static class T2 extends T1 {
		private String name;
		public T2(String newname, String oldname) {
			super(oldname);
			this.name = newname;
		}
		public String toString() {
			return super.toString() + " {newname=" + name + "}";
		}
	}
	
	public static void main2(String[] args) throws Exception {
		try {
			Object o = Class.forName("org.apache.log4j.Logger");
			System.out.println(o);
		} catch (Exception e) {
			System.out.println(e);
		}
	}
	public static void main0(String[] args) throws Exception {
		//Object o = new T2("T2", "main");
		//Object o = T2.class.getConstructors()[0];
		Object o = T2.class.getConstructor(String.class, String.class).newInstance("TT", "mainmain");
		
		System.out.println(o);
	}
	public static void main1(String[] args) throws Exception {
		Class<?> cls = Class.forName("sun.reflect.ReflectionFactory");
		System.out.println(cls);
		Object o = cls.getMethod("getReflectionFactory").invoke(null);
		System.out.println(o);
	}
	public static void main(String[] args) throws Exception {
		System.out.println("starting pdf...");
		
		//String pdf_url = "http://www.viacaoalvorada.com.br/html/modules/uploader/index.php?action=downloadfile&filename=horariosv.pdf";
		String pdf_path = "D:/download/horariosv.pdf";
		
		PDDocument doc = PDDocument.load(pdf_path);
		
		String text = new PDFTextStripper().getText(doc);
		
		System.out.println("length:" + text.length());
		
		System.out.println("pdf end");
    }
	
}