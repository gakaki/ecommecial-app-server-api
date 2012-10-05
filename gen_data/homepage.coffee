

fs       = require 'fs'
parser   = require 'xml2json'
data     = fs.readFileSync 'homepage.xml'
json     = parser.toJson data
console.log json
obj      = JSON.parse json
datainfo = obj.shopex.info.data_info
console.dir datainfo


