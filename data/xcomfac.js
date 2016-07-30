var csvParse = require('csv-parse');
var libxml = require("libxmljs");
var jsdom = require('jsdom');
var $ = require('jquery');
const fs = require('fs');
var _ = require('lodash');

const infile = './comm-facilities.geojson';

/* read & parse csv */
new Promise((resolve, reject) => {
    /* read data from file */
    fs.readFile(infile, (err, data) => {
        if (err) reject(err);

        resolve(data);
    });
}).then(function (strData) {
    var data = JSON.parse(strData);

    var facilities = _.slice(data.features, 0).map(function(feature) {
        var place = {
            name: feature.properties.name.replace(/'/g, "&quot;"),
            lat: feature.geometry.coordinates[0],
            lng: feature.geometry.coordinates[1]
        };

        var description = feature.properties.description.replace(/\n/g, '').replace(/\r/g, '');

        var matcher = /<td>([^<]*)<\/td> *<td>([^<]*)<\/td>/g;

        var val;

        while ((val = matcher.exec(description)) !== null)  {
            var heading = val[1];
            var value = val[2];

            place[heading] = value.replace(/'/g, "&quot;");
        }

        return place;
    });

    return facilities;
}).then(function(facilities) {
    var sql = 'BEGIN; insert into community_facilities (name, FEATURETYPE, TYPE, DESCRIPTION, position) values ';

    var rows = [];

    facilities.map(function(facility) {
        var row = [
            facility.name, facility.FEATURETYPE, facility.TYPE, facility.DESCRIPTION
        ];

        var _sql = " ('" + row.join("','") + "',";

        _sql += "GeomFromText('POINT(" + facility.lat + " " + facility.lng + ")',1)";

        rows.push(_sql + ')');
    });

    sql += rows.join(',');

    sql += '; COMMIT;';

    console.log(sql);
}).catch(function(e) {
    console.log(e);
});