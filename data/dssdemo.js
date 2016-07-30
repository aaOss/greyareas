var csvParse = require('csv-parse');
const fs = require('fs');

const csvFile = './dss-demographics.csv';

/* read & parse csv */
new Promise((resolve, reject) => {
    /* read data from file */
    fs.readFile(csvFile, (err, data) => {
        if (err) reject(err);

        resolve(data);
    });
}).then(function (strData) {
    /* parse csv to js arrays */
    return new Promise(function (resolve, reject) {
        csvParse(strData, function (err, rows) {
            if (err) reject(err);

            resolve(rows);
        });
    });
}).then(function(data) {
    var sql = 'BEGIN;';
    
    sql += 'insert into dss_demographics (postcode, age_pension, seniors_health_card) values ';

    var values = [];

    data.map(function(row) {
        values.push("('" + row[0] + "','" + row[3] + "','" + row[19] + "')");
    });

    sql += values.join(',');
    
    sql += ';COMMIT;';

    console.log(sql);
});