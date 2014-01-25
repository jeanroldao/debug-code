import java.io.*;
import java.util.*;
import java.lang.reflect.*;

public abstract class Teste48 {

	public static void main(String[] args) throws Exception {
		try {
			System.out.println("-helo-");
			
			byte c;
			DataInputStream input = new DataInputStream(System.in);
			while ((c = input.readByte()) != 'r') {
				System.out.print((char) c);
			}
			System.out.println("-bye-");
		} catch (Exception e) {
			System.out.println(e);
		}
	}
	
}