/*
-input-
* 0 0 0
* * * *
* 0 * 0
* * * 0

-output-
* 4 3 2 
* * * * 
* 8 * 4 
* * * 2 

-------
*/
import java.util.Scanner;
import java.util.ArrayList;
import java.io.InputStream; 
import java.io.FileInputStream; 
import java.lang.StringBuilder; 

public class CampoMinado {

    static char[][] mapOrig;
    static char[][] mapRadar;
	static boolean[][] openArea;
	
	static int qt_minas = 0;
    
    static int tamX = 4;
    static int tamY = 4;
	
	static InputStream input;
	
	static String rawMap;
	
	static boolean gameOver = false;
	
	static Scanner scan;

    public static void main(String[] args) throws Exception	{
        //System.out.println("Hello World");
		/*
		if (args.length > 0) {
			input = new FileInputStream(args[0]);
		} else {
			input = System.in;
		}*/
		
		scan = new Scanner(System.in);
		
		rawMap = GerarCampoMinado(args);
        scanMinner();
    }
    
    static void scanMinner() {
        mapOrig = getMap();
        mapRadar = new char[tamX][tamY];
        openArea = new boolean[tamX][tamY];


        for (int i = 0; i < tamX; i++) {
            for (int k = 0; k < tamY; k++) {
                mapRadar[i][k] = countMinesInPoint(k, i);
				if (mapRadar[i][k] == '*') {
					qt_minas++;
				}
            }
        }

        //printMap(mapOrig);
        //System.out.println("---------------------------");		
		update();
    }
	
	static void readGameInput() {
		String coord = scan.nextLine();
		int lin = coord.charAt(0) - 65;
		int col = coord.charAt(1) - 65;
		//System.out.println(lin+"*"+col);
		checkArea(col, lin);
		update();
	}
	
	static void checkArea(int x, int y) {
		openArea[y][x] = true;
		//mapRadar[y][x]
		if (mapRadar[y][x] == '*') {
			String go = "GAME OVER!";
			System.out.println(go);
			//go = "" + (1/0);
			gameOver = true;
			//throw new Exception(go);
		} 
		
		//echo("["+y+":"+x+"]"+(mapRadar[y][x] == ' '?"1":"0")+"\n");
		if (mapRadar[y][x] == ' ') {
			for (int i = y - 1; i <= y+1; i++) {
				for (int k = x - 1; k <= x + 1; k++) {
					try {
						//echo("["+y+":"+x+"]["+i+":"+k+"]"+(openArea[y][x]?"1":"0")+"\n");
						if (!openArea[i][k]) {
							checkArea(k, i);
						}
					} catch(Exception e) {}
				}
			}
			
		}
	}
	
	static void update() {
		System.out.println("minas="+qt_minas);
        printMap(mapRadar);
		
		if (!gameOver) {
			readGameInput();
		}
	}

    static char countMinesInPoint(int x, int y) {
        int res = 0;
        
        if (mapOrig[y][x] == '*') {
            return '*';
        }
        
        for (int i = y - 1; i <= y+1; i++) {
            for (int k = x - 1; k <= x + 1; k++) {
                try {
                    if (mapOrig[i][k] == '*') {
                        res++;
                    }
                } catch(Exception e) {}
            }
        }
        if (res == 0) {
		  return ' ';
		}
        
        return (char)(res+"").charAt(0);
    }

    static void printMap(char[][] map) {
		char nomecoluna = 'A';
		System.out.print(' ');
		//for (char c : map[0]) {
		for (int i = 0; i < map[0].length; i++) {
            System.out.print(nomecoluna++);
        }
		System.out.println();
		char nomelinha = 'A';
        //for (char[] linha : map) {
		for (int i = 0; i < map.length; i++) {
			System.out.print(nomelinha++);
            //for (char c : linha) {
			for (int k = 0; k < map[i].length; k++) {
				if (openArea[i][k]) {
					System.out.print(map[i][k]);
				} else {
					System.out.print('#');
				}
            }
            System.out.println();
        }
    }

    static char[][] getMap() {
        /*
        char[][] map = new char[tamX][tamY];    
        Scanner scan = new Scanner(input);

        //scan.hasNextLine()
        for (int i = 0; scan.hasNextLine() && i < tamX; i++) {
            String linha = scan.nextLine();
            linha = linha.replace(" ", "");
            //System.out.println(linha.length());
            map[i] = linha.toCharArray();
        }

        return map;
        */
        
        //ArrayList<char[]> map = new ArrayList<char[]>();
		
		/*
		String stringMap = "";
        
        Scanner scan = new Scanner(input);
		String linha;
        while (scan.hasNextLine() && (linha = scan.nextLine()).length() > 0 ) {
            //linha = linha.replace(" ", "");
			stringMap += linha + "\n";
            //System.out.println(linha.length());
            //map.add(linha.toCharArray());
            
        }
		*/
		
		String[] arrayMap = rawMap.split("\n");
		//echo(rawMap);
		tamX = arrayMap.length;
		tamY = arrayMap[0].length();
		char[][] map = new char[tamX][tamY];
		//for (String linha : arrayMap) 
		for (int i = 0; i < tamX; i++) {
			map[i] = arrayMap[i].toCharArray();
		}
        
        return map;
		
    }
    
    static void scanner() {
        Scanner scan = new Scanner(System.in);
        
        while (scan.hasNextLine()) {
          String linha = scan.nextLine();
          System.out.println(linha);
        }
    }

	
    static String GerarCampoMinado(String[] args) throws Exception	{
		// args opcionais = (tamX = 10, tamY = tamX, num_minas = (tamX * tamY) / 10)
		
		StringBuilder ret = new StringBuilder();
		int tamX = 10;
		if (args.length >= 1) {
			tamX = Integer.parseInt(args[0]);
		}
		
		int tamY = tamX;
		if (args.length >= 2) {
			tamY = Integer.parseInt(args[1]);
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
					ret.append("*");
				} else {
					ret.append("0");
				}
			}
			ret.append("\n");
		}
		
		return ret.toString();
    }
	
	static int getIntRand(int ini, int limit) {
		return (int) (ini + Math.random() * limit);
	}
	
	static void echo(Object s) {
		System.out.print(s);
	}

} 