import java.util.*;
import java.io.*;
import java.sql.*;

public class Teste24 implements Serializable {
	
	public static final char VALUE_SEPARATOR = ',';
	public static final char LINE_SEPARATOR = '\n';
	
	public static void main(String[] args) throws Exception {
		
		Scanner scan = new Scanner(new FileInputStream(args[0]));
		//Scanner scan = new Scanner(System.in);
		//System.out.println(new File(args[0]).length());
		StringBuilder sb = new StringBuilder();
		while (scan.hasNextLine()) {
			sb.append(scan.nextLine());
			if (scan.hasNextLine()) {
				sb.append(LINE_SEPARATOR);
			}
		}
		
		ArrayList<ArrayList<String>> table = parseCsvString(sb);
		sb = null;
		
		int i = 0;
		ArrayList<String> header = null;
		System.out.println('[');
		for (ArrayList<String> row : table) {
			if (header == null) {
				header = row;
			} else {
				System.out.println("  {");
				
				for (int k = 0; k < row.size(); k++) {
					System.out.print("    \"");
					System.out.print(header.get(k).replace("\"", "\\\"").replace("\n", "\\n"));
					System.out.print("\" : \"");
					System.out.print(row.get(k).replace("\"", "\\\"").replace("\n", "\\n"));
					System.out.print('"');
					if (k + 1 < row.size()) {
						System.out.print(',');
					}
					System.out.println();
				}
				System.out.print("  }");
				if (i + 1 < table.size()) {
					System.out.print(',');
				}
				System.out.println();
			}
			i++;
		}
		System.out.println(']');
		System.out.println("done! ("+table.size()+" convertidos!)");
		new Scanner(System.in).nextLine();
		//Thread.sleep(1000);
	}
	
	private static ArrayList<ArrayList<String>> parseCsvString(CharSequence csv) {
        if (csv == null) {
            return null;
        }
        ArrayList<ArrayList<String>> table = new ArrayList<ArrayList<String>>();
        ArrayList<String> row = new ArrayList<String>();
		
        StringBuilder curVal = new StringBuilder();
        boolean inquotes = false;
        for (int i = 0; i < csv.length(); i++) {
			System.gc();
            char ch = csv.charAt(i);
            if (inquotes) {
                if (ch == '"') {
                    inquotes = false;
                } else {
                    curVal.append(ch);
                }
            } else {
                if (ch == '"') {
                    inquotes = true;
                    if (curVal.length() > 0) {
                        //if this is the second quote in a value, add a quote
                        //this is for the double quote in the middle of a value
                        curVal.append('"');
                    }
                } else if (ch == VALUE_SEPARATOR) {
                    row.add(curVal.toString());
                    curVal = new StringBuilder();
                } else if (ch == LINE_SEPARATOR) {
                    row.add(curVal.toString());
                    curVal = new StringBuilder();
					table.add(row);
					row = new ArrayList<String>();
                } else {
                    curVal.append(ch);
                }
            }
        }
        row.add(curVal.toString());
		table.add(row);
        return table;
    }
}

