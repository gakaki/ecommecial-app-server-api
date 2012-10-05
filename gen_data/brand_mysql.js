// Generated by CoffeeScript 1.3.1
(function() {
  var IS_DEBUG, brand2mysql, connection, parser, share, xml2object;

  IS_DEBUG = 1;

  if (!IS_DEBUG) {
    console.log = function() {};
  }

  share = require('./share');

  connection = share.connection;

  connection.connect();

  connection.query("use shopex");

  connection.query("select * from sdb_brand", function(err, rows, fields) {
    if (err) {
      throw err;
    }
  });

  brand2mysql = function(brands) {
    var all_brands;
    all_brands = [];
    brands.forEach(function(brand) {
      var item, tmp, _i, _len, _results;
      tmp = brand.brand;
      _results = [];
      for (_i = 0, _len = tmp.length; _i < _len; _i++) {
        item = tmp[_i];
        _results.push(all_brands.push(item));
      }
      return _results;
    });
    return all_brands.map(function(brand) {
      return console.log('brand', brand);
    });
  };

  xml2object = require("xml2object");

  parser = new xml2object(["shopex"], "brand_category.xml");

  parser.on("object", function(name, obj) {
    var brandIcons, brands, data;
    data = obj.info.data_info;
    brands = data.brands.tag;
    brandIcons = data.brandIcons.tag;
    return brand2mysql(brands);
  });

  parser.on("end", function(name, obj) {
    return console.log("Finished parsing xml!");
  });

  parser.start();

  connection.end(function(err) {
    return console.log('connection is end', err);
  });

}).call(this);