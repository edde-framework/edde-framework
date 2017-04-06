import {IApplication} from "Api/Application/Edde.Api.Application.IApplication";
import {CheckboxControl} from "../Control/Edde.Common.Control.CheckboxControl";

export class Application implements IApplication {
	run(): void {
		let checkbox = new CheckboxControl();
		checkbox.update();
		console.log('foobar');
	}
}
