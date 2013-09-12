#include <stdio.h>

int main()
{
	int i, k, lim;
	for (i = 0; i < 1000000; i++) {
		lim = i * 10000;
		for (k = 0; k < lim; k++);
		printf("%d\n", k);
	}	
	return 0;
}
