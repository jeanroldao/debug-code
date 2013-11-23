import java.util.*;
import java.io.*;

public class Teste15 {

	static int intval(double v) {
		return new Double(v).intValue();
	}
	
	static String padLeft(String s, int n) {
		//return String.format("%1$" + n + "s", s);  
		//return String.format("%0" + n + "d", s);
		while (s.length() < n) {
			s = "0" + s;
		}
		return s;
	}

	static String percentToColor1(int percent){
		int minBrightness = 160;
		int maxBrightness = 255;

		// Remainins?
		double brightness = (double)((double)((minBrightness-maxBrightness)/(100f))*percent+maxBrightness);
		double first = (double)(1-(double)(percent/100f))*brightness;
		double second = (double)(percent/100f)*brightness;

		// Find the influence of the middle color (yellow if 1st and 2nd are red and green)
		double diff = (double)Math.abs(first-second);
		double influence = (double)(brightness-diff)/2;
		int ifirst = intval(first + influence);
		int isecond = intval(second + influence);

		// Convert to HEX, format and return
		String sfirst = Integer.toHexString(ifirst);
		//System.out.println(ifirst + "/" + sfirst);
		String firstHex = padLeft(sfirst,2);
		
		String ssecond = Integer.toHexString(isecond);
		//System.out.println(isecond + "/" + ssecond);
		String secondHex = padLeft(ssecond,2);

		return firstHex + secondHex + "00";
	}
	
	static String[] results = {"ff0000", "fe0500", "fd0a00", "fc0f00", "fb1400", "fa1900", "f91d00", "f82200", "f72700", "f62c00", "f53100", "f43500", "f33a00", "f23f00", "f14300", "f04800", "ef4c00", "ee5100", "ed5500", "ec5a00", "ec5e00", "eb6200", "ea6700", "e96b00", "e86f00", "e77300", "e67700", "e57b00", "e47f00", "e38300", "e28700", "e18b00", "e08f00", "df9300", "de9700", "dd9b00", "dc9e00", "dba200", "daa600", "d9aa00", "d9ad00", "d8b100", "d7b400", "d6b800", "d5bb00", "d4bf00", "d3c200", "d2c500", "d1c900", "d0cc00", "cfcf00", "cace00", "c5cd00", "c0cc00", "bbcb00", "b6ca00", "b1c900", "acc800", "a7c700", "a3c600", "9ec600", "99c500", "95c400", "90c300", "8bc200", "87c100", "82c000", "7ebf00", "79be00", "75bd00", "71bc00", "6cbb00", "68ba00", "64b900", "60b800", "5bb700", "57b600", "53b500", "4fb400", "4bb300", "47b300", "43b200", "3fb100", "3bb000", "38af00", "34ae00", "30ad00", "2cac00", "29ab00", "25aa00", "21a900", "1ea800", "1aa700", "17a600", "13a500", "10a400", "0da300", "09a200", "06a100", "03a000", "00a000"};
	static String percentToColor2(int percent) {
		return results[percent];
	}
	
	public static void main(String[] args) throws Exception{
		long ini = System.currentTimeMillis();
		//Thread.sleep(500);
		//System.out.println("color:" + percentToColor1(1));
		for (int i = 0; i <= 10000000; i++) {
			percentToColor2(i % 100);
		}
		//System.out.println("total:" + results.length);
		System.out.println((double)(System.currentTimeMillis() - ini) / 1000);
	} 
		
}

