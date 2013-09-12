import java.util.*;
import java.io.*;

public class Teste8 {

	public static class v1 {
		public static void list() {
			System.out.println("list");
		}
	}
	
	public static class v2 extends v1 {
		public static void list1() {
			System.out.println("list1");
		}
	}
	
	public static void main(String[] args) throws Exception {
		System.out.println(byte.class.getName());
		
		v2.list();
	}
	
}

