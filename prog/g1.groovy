/* Hello World in Groovy */

class g1 {
  String name
  def cb
  
  g1(name) {
    this.name = name
  }
  
  void setName(name) {
    if (cb != null && this.name != name) {
      cb(this.name, name)
    }
    this.name = name
  }
  
  public static void main(String[] args) {

	def p = new g1(1234)
	println(p.name)
	println(p.name.size())

	p.cb = { o, n -> 
	  println "changed from $o to $n" 
	}

	p.name = "1234"

	p.name = null

	p.name += "SN"

	println(p.name)
	println(p.name?.size())
  
  }
}
