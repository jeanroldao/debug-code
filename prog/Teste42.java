// aqui fica a mensagem!!!
import java.io.*;
import java.util.*;
import java.lang.reflect.*;

public abstract class Teste42 {

	public static void main(String[] args) throws Exception {
		try {
			System.out.println(Teste42.class.isAssignableFrom(null));
		} catch (Exception e) {
			System.out.println(e);
		}
	}
}