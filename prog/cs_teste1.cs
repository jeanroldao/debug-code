using System.IO;
using System;

class cs_teste1
{
    static void Main()
    {
		int i, k, lim;
		for (i = 0; i < 1000000; i++) {
			lim = i * 10000;
			for (k = 0; k < lim; k++);
			Console.WriteLine(k);
		}	
    }
}