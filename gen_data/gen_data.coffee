#sudo npm install -g mysql@2.0.0-alpha2 sequelize jugglingdb mysql-native mysql-libmysqlclient

#sys = require 'sys'
IS_DEBUG = 1
unless IS_DEBUG
	console.log = ()->
		
share = require './share'

connection = share.connection
connection.connect()
connection.query "use shopex"

brand2mysql = (brands)->
	all_brands = []
	brands.forEach (brand)->
		tmp = brand.brand
		for item in tmp
			#console.log 'item',item
			all_brands.push item
	#console.log 'all_brands',all_brands
	brands_name_insql_arr = []
	all_brands.map (brand)->
		#console.log 'brand',brand
		insert_data =
			brand_id : brand.brandId
			brand_name:brand.name
			brand_logo:brand.image
		console.log 'insert_data',insert_data
		connection.query "replace into sdb_brand set ?", insert_data , (err, rows, fields) ->
			throw err  if err
			console.log "Query result: ", err, rows, fields

gen_join_in_sql = (brands)->
		all_brands = []
		brands.forEach (brand)->
			tmp = brand.brand
			for item in tmp
				all_brands.push item
		brands_name_insql_arr = []
		all_brands.map (brand)->
			brand_name = "'#{brand.name}'"
			brands_name_insql_arr.push brand_name			#(",\n")
		tmp = brands_name_insql_arr.join(",")

classify_brands = (brands,classify_name)->
	hot = []
	normal = []
	for	item in brands.brand
		if item.type =='hot'
			hot.push "'#{item.name}'"
		else
			normal.push "'#{item.name}'"
	classify_name = 
		classify_name:classify_name
		hot:"#{hot.join(',')}"
		normal:"#{normal.join(',')}"

gen_brand_classify = (brands)->
	brands.map (brand)->
		res = classify_brands brand,brand.name	#'运动户外'
		console.log 'res',res

xml2object = require "xml2object" 
parser     = new xml2object [ "shopex"], "brand_category.xml"

parser.on "object", (name, obj) ->
	data 				= obj.info.data_info
	brands 			= data.brands.tag
	brandIcons 	= data.brandIcons.tag
	#console.log 'brands',brands
	brand2mysql brands
	brands_in_sql	 = gen_join_in_sql brands
	console.log 'brands_in_sql',brands_in_sql
	gen_brand_classify brandIcons
	

parser.on "end", (name, obj) ->
  console.log "Finished parsing xml!"

parser.start()

# 
# connection.end (err)->
# 	console.log 'connection is end',err
