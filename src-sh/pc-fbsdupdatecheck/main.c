#include <sys/types.h>
#include <sys/fcntl.h>
#include <err.h>
#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <unistd.h>

int main()
{
	system("touch /tmp/.fbsdup-lock");
	system("cat /usr/sbin/freebsd-update | sed 's|! -t 0|-z '1'|g' | /bin/sh -s 'fetch'");
	system("rm /tmp/.fbsdup-lock");
	return 0;
}
