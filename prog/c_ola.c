#include <stdio.h>

main()
{
    //printf("ola!");
	int i;
	char * msg = "ola %d! \n";
	for (i = 0; i < 10; i++) {
		printf(msg, i);
	}
}
