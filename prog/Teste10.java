import java.util.*;
import java.io.*;

public class Teste10 {
	
	static Scanner scan = new Scanner(System.in);
	
	public static void main(String[] args) throws Exception {
		
		File f = new File("Teste10_nome.txt");
		System.out.println("exits? " + (f.exists() ? "sim" : "nao"));
		System.out.println("isFile? " + (f.isFile() ? "sim" : "nao"));
		System.out.println("length? " + f.length() );
		scan.nextLine();
		f.createNewFile();
		
		//FileWriter fw = new FileWriter(f.getAbsoluteFile());
		//BufferedWriter bw = new BufferedWriter(fw);
		//bw.write(content);
		//bw.close();
		
		System.out.println("exits? " + (f.exists() ? "sim" : "nao"));
		System.out.println("isFile? " + (f.isFile() ? "sim" : "nao"));
		System.out.println("length? " + f.length() );
		scan.nextLine();
		f.delete();
		System.out.println("deleted");
		scan.nextLine();
	}
	
}

