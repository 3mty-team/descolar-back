job("Qodana") {
   container("jetbrains/qodana-php") {
      env["QODANA_TOKEN"] = Secrets("qodana-token")
      shellScript {
         content = """qodana"""
      }
   }
}