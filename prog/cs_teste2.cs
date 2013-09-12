using System.IO;
using System;

class cs_teste2
{
    static unsafe void Main()
    {
		int* nums = stackalloc int[3];
		nums[0] = 13;
		nums[1] = 22;
		nums[2] = 31;
		calc(nums);
		Console.WriteLine(*nums);
    }
	
	static unsafe void calc(int * i) {
		Console.WriteLine(*(i + 1));
		*i = 10;
	}
}