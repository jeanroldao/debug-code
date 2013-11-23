<?php
fputs(STDOUT, "oi");
fseek(STDOUT, -2, SEEK_CUR);
sleep(1);
fputs(STDOUT, "e ai?\n");

//var_dump(stream_get_meta_data(STDOUT));