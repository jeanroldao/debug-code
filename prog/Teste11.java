import java.util.*;
import java.io.*;

public class Teste11 {
	
	public static void main(String[] args) throws Exception {
		PrintStream out = System.out;
		int i , k, lim;
		for (i = 0; i < 1000000; i++) {
			lim = i * 10000;
			for (k = 0; k < lim; k++);
			out.println(k);
		}
	}
	
}

