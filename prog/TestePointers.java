import java.util.*;
import java.io.*;

public class TestePointers {
	
	static Scanner scan = new Scanner(System.in);
	
	static int[] mem = new int[1024];
	
	static int p = 0;
	
	public static void main(String[] args) throws Exception {
		new TestePointers();
	}
	
	TestePointers() {
		
		int m = storeString("resultado");
		System.out.println(m);
		System.out.println(getString(m));
		
		int m1 = storeString("edfg");
		System.out.println(m1);
		System.out.println(getString(m1));
	}
	
	int storeString(String value) {
		
		int pos = p;
		char[] ch = value.toCharArray();
		for (int i = 0; i < ch.length; i++) {
			mem[p++] = ch[i];
		}
		mem[p++] = '\0';
		return pos;
	}
	
	String getString(int pos) {
		int size = sizeOf(pos);
		char[] ch = new char[size];
		for (int i = 0; i < size; i++) {
			ch[i] = (char)mem[pos + i];
		}
		
		return new String(ch);
	}
	
	int sizeOf(int pos) {
		int size = 0;
		while(mem[pos + size] != '\0') {
			size++;
		}
		return size;
	}
	
}

