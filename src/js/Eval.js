class Eval {

	static async create () {
		let x = JQConnect()
			.to('https://localhost/eval/api/eval/script/create')

		await x.getAsync()
		return x
	}

	static async fetch (filename) {
		let x = JQConnect()
			.to('https://localhost/eval/api/eval/script/fetch')
			.queryParam('file', filename)

		await x.getAsync()
		return x
	}

	static async save (filename, fileContent) {
		let x = JQConnect()
			.to('https://localhost/eval/api/eval/script')
			.queryParam('file', filename)
			.json({d: fileContent})

		await x.postAsync()
		return x
	}

	static async rename (oldName, newName) {
		let x = JQConnect()
			.to('https://localhost/eval/api/eval/script/rename')
			.queryParam('from', oldName)
			.queryParam('to', newName)

		await x.getAsync()
		return x
	}

	static async delete (filename) {
		let x = JQConnect()
			.to('https://localhost/eval/api/eval/script')
			.queryParam('file', filename)

		await x.deleteAsync()
		return x
	}

	static async list () {
		let x = JQConnect()
			.to('https://localhost/eval/api/eval/script')

		await x.getAsync()
		return x
	}

}