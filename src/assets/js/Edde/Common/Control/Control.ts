import {AbstractControl} from "./AbstractControl";

export class Control extends AbstractControl {
	update(): void {
		console.log('update in control!');
	}
}
