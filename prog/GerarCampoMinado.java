import java.util.Scanner;
import java.util.ArrayList;
import java.io.InputStream; 
import java.io.FileInputStream; 

public class GerarCampoMinado {

    public static void main(String[] args) throws Exception	{
		// args opcionais = (tamX = 10, tamY = tamX, num_minas = (tamX * tamY) / 10)
		int tamX = 10;
		if (args.length >= 1) {
			tamX = Integer.parseInt(args[0]);
		}
		
		int tamY;
		if (args.length >= 2) {
			tamY = Integer.parseInt(args[1]);
		} else {
			tamY = tamX;
		}
				
		int num_minas = (tamX * tamY) / 10;
		
		if (args.length >= 3) {
		  num_minas = Integer.parseInt(args[2]);
		}
		
		ArrayList<String> minas = new ArrayList<String>();
		
		while (minas.size() < num_minas) {
			int x = getIntRand(0, tamX);
			int y = getIntRand(0, tamY);
			String coord = x+"*"+y;
			//System.err.println(minas.size() + "-" + coord);
			if (!minas.contains(coord)) {
				minas.add(coord);
			}
		}
		
		for (int i = 0; i < tamY; i++) {
			for (int k = 0; k < tamX; k++) {
				//if (Math.random() > 0.9) {
				if (minas.contains(k+"*"+i)) {
					System.out.print("*");
				} else {
					System.out.print("0");
				}
			}
			System.out.println();
		}
    }
	
	static int getIntRand(int inicio, int limite) {
		return (int) (inicio + Math.random() * limite);
	}

} 