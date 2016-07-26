elvis = 'foo';

if (typeof elvis !== "undefined" && elvis !== null) {
  console.log("I knew it!");
}

race('foo', 'bar', 'baz');

cubes = (function() {
  var i, len, results;
  results = [];
  for (i = 0, len = list.length; i < len; i++) {
    num = list[i];
    results.push(math.cube(num));
  }
  return results;
})();

console.log(cubes.join('-'));
