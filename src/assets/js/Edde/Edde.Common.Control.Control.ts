import {AbstractControl} from "./Edde.Common.Control.AbstractControl";

export class Control extends AbstractControl {
	update(): void {
		console.log('update in control!');
	}
}
