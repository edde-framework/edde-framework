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
define(["require", "exports", "./Control"], function (require, exports, Control_1) {
	"use strict";
	exports.__esModule = true;
	var CheckboxControl = (function (_super) {
		__extends(CheckboxControl, _super);
		function CheckboxControl() {
			return _super !== null && _super.apply(this, arguments) || this;
		}

		return CheckboxControl;
	}(Control_1.Control));
	exports.CheckboxControl = CheckboxControl;
});
//# sourceMappingURL=CheckboxControl.js.map
