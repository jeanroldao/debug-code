<?php
include 'DataInput.php';

/**
 * reads a file using java's DataInputStream
 */
class DataInputStream implements DataInput {
	
	private $file;

	public function __construct($file) {
		if (!is_resource($file)) {
			throw new DataInputStreamException('Not a resource');
		}
		$this->file = $file;
	}
	
	public function __destruct() {
		//fclose($this->file);
	}
	
	public function readHex($bytes = 1) {
		$binarydata = $this->readChar($bytes);
		return unpack("H".($bytes*2)."hex", $binarydata)['hex'];
	}
	
	/**
	 * Reads one input byte and returns true if that byte is nonzero, false if that byte is zero.
	 * @return bool
	 */
	public function readBoolean() {
		return (bool) $this->readChar();
	}
	
	/**
	 * Reads and returns one input byte.
	 * @return byte
	 */
	public function readByte() {
		try {
			return hexdec($this->readHex(1));
		} catch (\DataInputStreamException $e) {
			return -1;
		}
		//return ord(fread($this->file, 1));
		//$ret = ord(fgetc($this->file));
		//var_dump($ret);
		//return $ret;
	}
	
	/**
	 * Reads two input bytes and returns a char value.
	 * @return char
	 */
	public function readChar($len = 1) {
		$ret = '';
		$fp = $this->file;
		for ($i = 0; $i < $len; $i++) {
			$c = fgetc($fp);
			if ($c === false) {
				throw new \DataInputStreamException(jstring("EOF"));
				//var_dump('EOF');
				//exit;
			}
			$ret .= $c;
		}
		return $ret;
		//return fread($this->file, $len);
	}
    
	/**
	 * Reads two input bytes and returns a short value.
	 * @return short
	 */
	function readShort() {
		$binarydata = $this->readChar(2);
		$n = +(unpack("s", strrev($binarydata))[1]);
		//var_dump($n);
		return +$n;
	}
	
	/**
	 * Reads four input bytes and returns an int value.
	 * @return int
	 */
	public function readInt() {
		//$binarydata = fread($this->file, 4);;
		//return hexdec(unpack("H8", $binarydata)[1]);
		$value = hexdec($this->readHex(4));
		//var_dump($value);
		while ($value > 2147483647) {
			$value -= 4294967296;
		}
		while ($value < -2147483648) {
			$value += 4294967296;
		}
		//var_dump($value);
		return $value;
	}
          
	/**
	 * Reads eight input bytes and returns a double value.
	 * @return double
	 */
	function readDouble() {
		return unpack('dd', strrev(fread($this->file, 8)))['d'];
	}
          
	/**
	 * Reads four input bytes and returns a float value.
	 * @return float
	 */
	function readFloat() {
		return +unpack('ff', strrev(fread($this->file, 4)))['f'];
	}
          
	/**
	 * Reads some bytes from an input stream and stores them into the buffer array b.
	 *
	 * Reads len bytes from an input stream.
	 *
	 * @return void
	 */
	function readFully(array &$b, $off = null, $len = null) {}
	
	/**
	 * Reads the next line of text from the input stream.
	 * @return string
	 */
    function readLine() {
		$line = fgets($this->file);
		//var_dump($line);sleep(1);
		return $line;
	}
          
	/**
	 * Reads eight input bytes and returns a long value.
	 * @return long
	 */
	function readLong() {
		//echo bcmul('9223372036854775808', 2);exit;
		/*
		static $cont = 0;
		if (++$cont == 4) {
			ob_end_clean();
			var_dump('readLong()');
			$l1 = $this->readHex(4);
			var_dump($l1);
			var_dump(sprintf('%0.0f', hexdec($l1)));
			$l2 = $this->readHex(4);
			var_dump($l2);
			var_dump(sprintf('%0.0f', hexdec($l2)));
			var_dump(bcadd(bcmul(hexdec($l1), 0x100000000), hexdec($l2)));
			exit;
		}
		//*/
		
		$l1 = $this->readHex(4);
		$l2 = $this->readHex(4);
		$result = bcadd(bcmul(hexdec($l1), 0x100000000), hexdec($l2));
		while (bccomp($result, '9223372036854775807') === 1) {
			$result = bcsub($result, '18446744073709551616');
		}
		return $result;
		//return sprintf('%0.0f', hexdec($this->readHex(8)));
	}
          
	/**
	 * Reads one input byte, zero-extends it to type int, 
	 * and returns the result, which is therefore in the range 0 through 255.
	 * @return int
	 */
	function readUnsignedByte() {
		return +$this->readByte();
	}
          
	/**
	 * Reads two input bytes and returns an int value in the range 0 through 65535.
	 * @return int
	 */
	function readUnsignedShort() {
		$a = unpack('Ca/Cb', fread($this->file, 2));
		return +$a['a'] << 8 | $a['b'];
		//$n = $this->readShort();
		//if ($n < 0) {
		//	$n += 65536;
		//}
		//return $n;
	}
          
	/**
	 * Reads in a string that has been encoded using a modified UTF-8 format.
	 * @return string
	 */
	function readUTF() {
		//$len = $this->readShort();
		$len = $this->readUnsignedShort();
		//println("($len)");
		if ($len == 0) {return '';}
		return utf8_decode($this->readChar($len));
	}
	
	/**
	 * Makes an attempt to skip over n bytes of data from the input stream, 
	 * discarding the skipped bytes.
	 * @return int
	 */
	function skipBytes($n) {
		fseek($this->file, $n, SEEK_CUR);
		return $n;
	}
}

class DataInputStreamException extends Exception {}