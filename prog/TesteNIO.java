import java.io.*;
import java.nio.*;
import java.nio.channels.*;

/**
 * Minimal NIO file-read test.
 * Exercises the exact code path SmallSQL uses to read .sdb files,
 * without any SmallSQL-specific bytecode in the way.
 *
 * Run: java TesteNIO  (or via php_javaClass.bat TesteNIO)
 * Expected: identical hex output from both
 */
public class TesteNIO {

    static void printHex(String label, byte[] data, int len) {
        System.out.print(label + ": ");
        for (int i = 0; i < len; i++) {
            System.out.printf("%02x ", data[i] & 0xFF);
        }
        System.out.println("(" + len + " bytes)");
    }

    public static void main(String[] args) throws Exception {
        String path = "emp1/messages.sdb";

        // -------------------------------------------------------
        // Test 1: old-style FileInputStream (no NIO)
        // -------------------------------------------------------
        System.out.println("--- Test 1: FileInputStream ---");
        FileInputStream fis = new FileInputStream(path);
        byte[] buf1 = new byte[8];
        int n1 = fis.read(buf1);
        fis.close();
        printHex("fis.read(8)", buf1, n1);

        // -------------------------------------------------------
        // Test 2: FileChannel + heap ByteBuffer (what SmallSQL uses)
        // -------------------------------------------------------
        System.out.println("--- Test 2: FileChannel + HeapByteBuffer ---");
        FileInputStream fis2 = new FileInputStream(path);
        FileChannel fc = fis2.getChannel();
        System.out.println("channel open, size=" + fc.size() + " pos=" + fc.position());
        ByteBuffer bb = ByteBuffer.allocate(8);
        int n2 = fc.read(bb);
        System.out.println("fc.read returned " + n2);
        bb.flip();
        byte[] buf2 = new byte[n2 > 0 ? n2 : 0];
        bb.get(buf2);
        fc.close();
        fis2.close();
        printHex("fc.read(8)", buf2, buf2.length);

        // -------------------------------------------------------
        // Test 3: FileChannel + seek + read more bytes
        // -------------------------------------------------------
        System.out.println("--- Test 3: FileChannel seek + read ---");
        FileInputStream fis3 = new FileInputStream(path);
        FileChannel fc3 = fis3.getChannel();
        fc3.position(0);
        ByteBuffer bb3 = ByteBuffer.allocate(32);
        int n3 = fc3.read(bb3);
        System.out.println("fc3.read returned " + n3);
        bb3.flip();
        byte[] buf3 = new byte[n3 > 0 ? n3 : 0];
        bb3.get(buf3);
        fc3.close();
        fis3.close();
        printHex("fc3.read(32)", buf3, buf3.length);

        // -------------------------------------------------------
        // Test 4: RandomAccessFile (another path SmallSQL sometimes uses)
        // -------------------------------------------------------
        System.out.println("--- Test 4: RandomAccessFile ---");
        RandomAccessFile raf = new RandomAccessFile(path, "r");
        byte[] buf4 = new byte[8];
        int n4 = raf.read(buf4);
        raf.close();
        printHex("raf.read(8)", buf4, n4);

        System.out.println("--- ALL DONE ---");
    }
}
