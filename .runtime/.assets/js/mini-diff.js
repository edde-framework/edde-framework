/**
 * This is simple library build around simple diff protocol to apply changes to DOM tree without
 * pain.
 */
var MiniDiff = {
	Commands: {
		'add-node': function () {
			console.log('node');
			console.log(arguments);
		}
	},
	patch: function (commandList, root) {
		var length = commandList.length;
		for (var i = 0; i < length; i++) {
			command = commandList[i];
			this['Commands'][command['command']].apply(this, command['parameter-list'])
		}
	}
};
