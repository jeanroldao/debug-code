<?php

interface DataInput {
	
	/**
	 * Reads one input byte and returns true if that byte is nonzero, false if that byte is zero.
	 * @return bool
	 */
	function readBoolean();
	
	/**
	 * Reads and returns one input byte.
	 * @return byte
	 */
	function readByte();
	
	/**
	 * Reads two input bytes and returns a char value.
	 * @return char
	 */
	function readChar();
          
	/**
	 * Reads eight input bytes and returns a double value.
	 * @return double
	 */
	function readDouble();
          
	/**
	 * Reads four input bytes and returns a float value.
	 * @return float
	 */
	function readFloat();
          
	/**
	 * Reads some bytes from an input stream and stores them into the buffer array b.
	 *
	 * Reads len bytes from an input stream.
	 *
	 * @return void
	 */
	function readFully(array &$b, $off = null, $len = null); 
          
	/**
	 * Reads four input bytes and returns an int value.
	 * @return int
	 */
	function readInt();
	
	/**
	 * Reads the next line of text from the input stream.
	 * @return string
	 */
     function readLine();
          
	/**
	 * Reads eight input bytes and returns a long value.
	 * @return long
	 */
	function readLong();
    
	/**
	 * Reads two input bytes and returns a short value.
	 * @return short
	 */
	function readShort();
          
	/**
	 * Reads one input byte, zero-extends it to type int, 
	 * and returns the result, which is therefore in the range 0 through 255.
	 * @return int
	 */
	function readUnsignedByte();
          
	/**
	 * Reads two input bytes and returns an int value in the range 0 through 65535.
	 * @return int
	 */
	function readUnsignedShort();
          
	/**
	 * Reads in a string that has been encoded using a modified UTF-8 format.
	 * @return string
	 */
	function readUTF();
	
	/**
	 * Makes an attempt to skip over n bytes of data from the input stream, 
	 * discarding the skipped bytes.
	 * @return int
	 */
	function skipBytes($n);
}

?>