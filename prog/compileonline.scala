object HelloWorld {
   def main(args: Array[String]) {
      //println("Hello, world!")
      println(falar("jean", "oi mundo"))
   }
   
   def falar(quem: String, oque: String) = quem + ": " + oque
   
}
   