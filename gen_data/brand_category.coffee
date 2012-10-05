

xml2object = require "xml2object" 
parser     = new xml2object [ "shopex","brands","brandIcons" ], "brand_category.xml"

parser.on "object", (name, obj) ->
  console.log "Found an object: %s", name
  console.log obj
  console.log obj.info.data_info.brands
  console.log obj.info.data_info.brandIcons

parser.on "end", (name, obj) ->
  console.log "Finished parsing xml!"

parser.start()


fs       = require 'fs'
parser   = require 'xml2json'
data     = fs.readFileSync 'brand_category.xml'
json     = parser.toJson datainfo
obj      = JSON.parse json
datainfo = obj.shopex.info.data_info
console.dir datainfo
console.dir datainfo.brands.tag
console.dir datainfo.brandIcons.tag

