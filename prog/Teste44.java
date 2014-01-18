// aqui fica a mensagem!!!
import java.io.*;
import java.util.*;
import java.lang.reflect.*;

public abstract class Teste44 {

	public static void main(String[] args) throws Exception {
		try {
			
			System.out.println(System.mapLibraryName("nio"));
			System.out.println(System.mapLibraryName("nonexiste"));
		} catch (Exception e) {
			System.out.println(e);
		}
	}
	
}