

mysql = require("mysql")
connection = mysql.createConnection(
  host: "127.0.0.1"
  user: "root"
  password: ""
)

exports.mysql = mysql
exports.connection = connection