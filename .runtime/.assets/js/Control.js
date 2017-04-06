var __extends = (this && this.__extends) || (function () {
		var extendStatics = Object.setPrototypeOf ||
			({__proto__: []} instanceof Array && function (d, b) {
				d.__proto__ = b;
			}) ||
			function (d, b) {
				for (var p in b) {
					if (b.hasOwnProperty(p)) {
						d[p] = b[p];
					}
				}
			};
		return function (d, b) {
			extendStatics(d, b);
			function __() {
				this.constructor = d;
			}

			d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
		};
	})();
define(["require", "exports", "./AbstractControl"], function (require, exports, AbstractControl_1) {
	"use strict";
	exports.__esModule = true;
	var Control = (function (_super) {
		__extends(Control, _super);
		function Control() {
			return _super !== null && _super.apply(this, arguments) || this;
		}

		Control.prototype.update = function () {
			console.log('update in control!');
		};
		return Control;
	}(AbstractControl_1.AbstractControl));
	exports.Control = Control;
});
//# sourceMappingURL=Control.js.map
