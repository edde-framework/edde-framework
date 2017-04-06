import {IControl} from "./IControl";

export abstract class AbstractControl implements IControl {
	abstract update(): void;
}
