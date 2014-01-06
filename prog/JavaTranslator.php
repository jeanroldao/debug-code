<?php

trait JavaTranslator {
	private function translateHexOpcode($hexOpcode, &$i) {
		$input = $this->input;

		$hexOpcode = hexdec($hexOpcode);
		switch ($hexOpcode) {
			case 0x00:
				return [1, 'nop'];
			case 0x01:
				return [1, 'aconst_null'];
			case 0x02:
				return [1, 'iconst_m1'];
			case 0x03:
			case 0x04:
			case 0x05:
			case 0x06:
			case 0x07:
			case 0x08:
				return [1, 'iconst_'.($hexOpcode-3 & bindec('000111'))];
			case 0x09:
			case 0x0a:
				return [1, 'lconst_'.($hexOpcode-1 & bindec('000111'))];
			case 0x0b:
			case 0x0c:
			case 0x0d:
				return [1, 'fconst_'.($hexOpcode-3 & bindec('000111'))];
			case 0x0e:
			case 0x0f:
				return [1, 'dconst_'.($hexOpcode-6 & bindec('000111'))];
			case 0x10:
				$i++;
				return [2, 'bipush', $input->readByte()];
			case 0x11:
				$i += 2;
				return [3, 'sipush', $input->readShort()];
			case 0x12:
				$i++;
				return [2, 'ldc', $input->readByte()];
			case 0x13:
				$i += 2;
				return [3, 'ldc_w', $input->readShort()];
			case 0x14:
				$i += 2;
				return [3, 'ldc2_w', $input->readShort()];
			case 0x15:
				$i++;
				return [2, 'iload', $input->readByte()];
			case 0x16:
				$i++;
				return [2, 'lload', $input->readByte()];
			case 0x17:
				$i++;
				return [2, 'fload', $input->readByte()];
			case 0x18:
				$i++;
				return [2, 'dload', $input->readByte()];
			case 0x19:
				$i++;
				return [2, 'aload', $input->readByte()];
			case 0x1a:
			case 0x1b:
			case 0x1c:
			case 0x1d:
				return [1, 'iload_'.($hexOpcode-2 & bindec('000111'))];
			case 0x1e:
			case 0x1f:
			case 0x20:
			case 0x21:
				return [1, 'lload_'.($hexOpcode-6 & bindec('000111'))];
			case 0x22:
			case 0x23:
			case 0x24:
			case 0x25:
				return [1, 'fload_'.($hexOpcode-2 & bindec('000111'))];
			case 0x26:
			case 0x27:
			case 0x28:
			case 0x29:
				return [1, 'dload_'.($hexOpcode-6 & bindec('000111'))];
			case 0x2a:
			case 0x2b:
			case 0x2c:
			case 0x2d:
				return [1, 'aload_'.($hexOpcode-2 & bindec('000111'))];
			case 0x2e:
				return [1, 'iaload'];
			case 0x2f:
				return [1, 'laload'];
			case 0x30:
				return [1, 'faload'];
			case 0x31:
				return [1, 'daload'];
			case 0x32:
				return [1, 'aaload'];
			case 0x33:
				return [1, 'baload'];
			case 0x34:
				return [1, 'caload'];
			case 0x35:
				return [1, 'saload'];
			case 0x36:
				$i++;
				return [2, 'istore', $input->readByte()];
			case 0x37:
				$i++;
				return [2, 'lstore', $input->readByte()];
			case 0x38:
				$i++;
				return [2, 'fstore', $input->readByte()];
			case 0x39:
				$i++;
				return [2, 'dstore', $input->readByte()];
			case 0x3a:
				$i++;
				return [2, 'astore', $input->readByte()];
			case 0x3b:
			case 0x3c:
			case 0x3d:
			case 0x3e:
				return [1, 'istore_'.($hexOpcode-3 & bindec('000111'))];
			case 0x3f:
			case 0x40:
			case 0x41:
			case 0x42:
				return [1, 'lstore_'.($hexOpcode+1 & bindec('000111'))];
			case 0x43:
			case 0x44:
			case 0x45:
			case 0x46:
				return [1, 'fstore_'.($hexOpcode-3 & bindec('000111'))];
			case 0x47:
			case 0x48:
			case 0x49:
			case 0x4a:
				return [1, 'dstore_'.($hexOpcode+1 & bindec('000111'))];
			case 0x4b:
			case 0x4c:
			case 0x4d:
			case 0x4e:
				return [1, 'astore_'.($hexOpcode-3 & bindec('000111'))];
			case 0x4f:
				return [1, 'iastore'];
			case 0x50:
				return [1, 'lastore'];
			case 0x51:
				return [1, 'fastore'];
			case 0x52:
				return [1, 'dastore'];
			case 0x53:
				return [1, 'aastore'];
			case 0x54:
				return [1, 'bastore'];
			case 0x55:
				return [1, 'castore'];
			case 0x56:
				return [1, 'sastore'];
			case 0x57:
				return [1, 'pop'];
			case 0x58:
				return [1, 'pop2'];
			case 0x59:
				return [1, 'dup'];
			case 0x5a:
				return [1, 'dup_x1'];
			case 0x5b:
				return [1, 'dup_x2'];
			case 0x5c:
				return [1, 'dup2'];
			case 0x5e:
				return [1, 'dup2_x2'];
			case 0x5f:
				return [1, 'swap'];
			case 0x60:
				return [1, 'iadd'];
			case 0x61:
				return [1, 'ladd'];
			case 0x62:
				return [1, 'fadd'];
			case 0x63:
				return [1, 'dadd'];
			case 0x64:
				return [1, 'isub'];
			case 0x65:
				return [1, 'lsub'];
			case 0x66:
				return [1, 'fsub'];
			case 0x67:
				return [1, 'dsub'];
			case 0x68:
				return [1, 'imul'];
			case 0x6a:
				return [1, 'fmul'];
			case 0x6b:
				return [1, 'dmul'];
			case 0x6c:
				return [1, 'idiv'];
			case 0x6d:
				return [1, 'ldiv'];
			case 0x6e:
				return [1, 'fdiv'];
			case 0x6f:
				return [1, 'ddiv'];
			case 0x69:
				return [1, 'lmul'];
			case 0x70:
				return [1, 'irem'];
			case 0x71:
				return [1, 'lrem'];
			case 0x72:
				return [1, 'frem'];
			case 0x73:
				return [1, 'drem'];
			case 0x74:
				return [1, 'ineg'];
			case 0x75:
				return [1, 'lneg'];
			case 0x76:
				return [1, 'lshl'];
			case 0x77:
				return [1, 'dneg'];
			case 0x78:
				return [1, 'ishl'];
			case 0x79:
				return [1, 'lshl'];
			case 0x7a:
				return [1, 'ishr'];
			case 0x7b:
				return [1, 'lshr'];
			case 0x7c:
				return [1, 'iushr'];
			case 0x7d:
				return [1, 'lushr'];
			case 0x7e:
				return [1, 'iand'];
			case 0x7f:
				return [1, 'land'];
			case 0x80:
				return [1, 'ior'];
			case 0x81:
				return [1, 'lor'];
			case 0x82:
				return [1, 'ixor'];
			case 0x83:
				return [1, 'lxor'];
			case 0x84:
				$i += 2;
				$var = $input->readByte();
				$inc = $input->readByte();
				if ($inc > 127) {
					$inc -= 256;
				}
				return [3, 'iinc', $var, $inc];
			case 0x85:
				return [1, 'i2l'];
			case 0x86:
				return [1, 'i2f'];
			case 0x87:
				return [1, 'i2d'];
			case 0x88:
				return [1, 'l2i'];
			case 0x89:
				return [1, 'l2f'];
			case 0x8a:
				return [1, 'l2d'];
			case 0x8b:
				return [1, 'f2i'];
			case 0x8c:
				return [1, 'f2l'];
			case 0x8d:
				return [1, 'f2d'];
			case 0x8e:
				return [1, 'd2i'];
			case 0x8f:
				return [1, 'd2l'];
			case 0x90:
				return [1, 'd2f'];
			case 0x91:
				return [1, 'i2b'];
			case 0x92:
				return [1, 'i2c'];
			case 0x93:
				return [1, 'i2s'];
			case 0x94:
				return [1, 'lcmp'];
			case 0x95:
				return [1, 'fcmpl'];
			case 0x96:
				return [1, 'fcmpg'];
			case 0x97:
				return [1, 'dcmpl'];
			case 0x98:
				return [1, 'dcmpg'];
			case 0x99:
				$i += 2;
				return [3, 'ifeq', $input->readShort()];
			case 0x9a:
				$i += 2;
				return [3, 'ifne', $input->readShort()];
			case 0x9b:
				$i += 2;
				return [3, 'iflt', $input->readShort()];
			case 0x9c:
				$i += 2;
				return [3, 'ifge', $input->readShort()];
			case 0x9d:
				$i += 2;
				return [3, 'ifgt', $input->readShort()];
			case 0x9e:
				$i += 2;
				return [3, 'ifle', $input->readShort()];
			case 0x9f:
				$i += 2;
				return [3, 'if_icmpeq', $input->readShort()];
			case 0xa0:
				$i += 2;
				return [3, 'if_icmpne', $input->readShort()];
			case 0xa1:
				$i += 2;
				return [3, 'if_icmplt', $input->readShort()];
			case 0xa2:
				$i += 2;
				return [3, 'if_icmpge', $input->readShort()];
			case 0xa3:
				$i += 2;
				return [3, 'if_icmpgt', $input->readShort()];
			case 0xa4:
				$i += 2;
				return [3, 'if_icmple', $input->readShort()];
			case 0xa5:
				$i += 2;
				return [3, 'if_acmpeq', $input->readShort()];
			case 0xa6:
				$i += 2;
				return [3, 'if_acmpne', $input->readShort()];
			case 0xa8:
				$i += 2;
				return [3, 'jsr', $input->readShort()];
			case 0xa9:
				$i++;
				return [3, 'ret', $input->readByte()];
			case 0xaa:
				$len = $i - 1;
				while(($i + 1) % 4 != 0) {
					$i++;
					$hexOpcode = $input->readHex();
				}
				
				$i += 4;
				$default = $input->readInt();
				
				$i += 4;
				$low = $input->readInt();
				
				$i += 4;
				$high = $input->readInt();

				$length = ($high - $low) + 1;
				
				$i += $length * 4;
				
				$adresses = ['default' => $default];
				for ($addr = 0; $addr < $length; $addr++) {
					$adresses[$low + $addr] = $input->readInt();
				}
				//$bytes = $input->readBytes($length * 4 );
				return [$i - $len, 'tableswitch', $adresses];
				//exit;
			case 0xa7:
				$i += 2;
				return [3, 'goto', $input->readShort()];
			case 0xab:
				$len = $i - 1;
				while(($i + 1) % 4 != 0) {
					$i++;
					$hexOpcode = $input->readHex();
				}
				
				$i += 4;
				$default = $input->readInt();
				
				$i += 4;
				$npairs = $input->readInt();
				
				$i += $npairs * 8;
				
				$adresses = ['default' => $default];
				for ($addr = 0; $addr < $npairs; $addr++) {
					$offset = $input->readInt();
					$adresses[$offset] = $input->readInt();
				}
				//$bytes = $input->readBytes($length * 4 );
				return [$i - $len, 'lookupswitch', $adresses];
			case 0xac:
				return [1, 'ireturn'];
			case 0xad:
				return [1, 'lreturn'];
			case 0xae:
				return [1, 'freturn'];
			case 0xaf:
				return [1, 'dreturn'];
			case 0xb0:
				return [1, 'areturn'];
			case 0xb1:
				return [1, 'return'];
			case 0xb2:
				$i += 2;
				return [3, 'getstatic', $this->getStaticField($input->readShort())];
			case 0xb3:
				$i += 2;
				return [3, 'putstatic', $this->getStaticField($input->readShort())];
			case 0xb4:
				$i += 2;
				return [3, 'getfield', $this->getStaticField($input->readShort())];
			case 0xb5:
				$i += 2;
				return [3, 'putfield', $this->getStaticField($input->readShort())];
			case 0xb6:
				$i += 2;
				return [3, 'invokevirtual', $this->getMethod($input->readShort())];
			case 0xb7:
				$i += 2;
				return [3, 'invokespecial', $this->getMethod($input->readShort())];
			case 0xb8:
				$i += 2;
				return [3, 'invokestatic', $this->getMethod($input->readShort())];
			case 0xb9:
				$i += 4;
				return [5, 'invokeinterface', $this->getInterfaceMethod($input->readShort()), $input->readShort()];
			case 0xbb:
				$i += 2;
				return [3, 'new', $this->getClassName($input->readShort())];
			case 0xbc:
				$i++;
				static $types = [
					 4 => 'Z',//boolean
					 5 => 'C',//char
					 6 => 'F',//float
					 7 => 'D',//double
					 8 => 'B',//byte
					 9 => 'S',//short
					10 => 'I',//int
					11 => 'J' //long
				];
				return [2, 'newarray', $types[$input->readByte()]];
			case 0xbe:
				return [1, 'arraylength'];
			case 0xbd:
				$i += 2;
				return [3, 'anewarray', $this->getClassName($input->readShort())];
			case 0xbf:
				return [1, 'athrow'];
			case 0xc0:
				$i += 2;
				return [3, 'checkcast', $this->getClassName($input->readShort())];
			case 0xc1:
				$i += 2;
				//var_dump([3, 'instanceof', $this->getClassName($input->readShort())]);exit;
				return [3, 'instanceof', $this->getClassName($input->readShort())];
			case 0xc2:
				return [1, 'monitorenter'];
			case 0xc3:
				return [1, 'monitorexit'];
			case 0xc5:
				$i += 3;
				return [4, 'multianewarray', $this->getClassName($input->readShort()), $input->readByte()];
			case 0xc6:
				$i += 2;
				return [3, 'ifnull', ($input->readShort())];
			case 0xc7:
				$i += 2;
				return [3, 'ifnonnull', ($input->readShort())];
			default:
				var_dump(dechex($hexOpcode));
				echo 'unknown opcode';
				exit;
				//return $hexOpcode;
		}
	}
}
