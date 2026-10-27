import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';

// Кнопки панели: [текст, подсказка, действие, активна ли]
const buttons = [
    ['H2', 'Заголовок', (e) => e.chain().focus().toggleHeading({ level: 2 }).run(), (e) => e.isActive('heading', { level: 2 })],
    ['H3', 'Подзаголовок', (e) => e.chain().focus().toggleHeading({ level: 3 }).run(), (e) => e.isActive('heading', { level: 3 })],
    ['Ж', 'Жирный', (e) => e.chain().focus().toggleBold().run(), (e) => e.isActive('bold')],
    ['К', 'Курсив', (e) => e.chain().focus().toggleItalic().run(), (e) => e.isActive('italic')],
    ['• Список', 'Маркированный список', (e) => e.chain().focus().toggleBulletList().run(), (e) => e.isActive('bulletList')],
    ['1. Список', 'Нумерованный список', (e) => e.chain().focus().toggleOrderedList().run(), (e) => e.isActive('orderedList')],
    ['Цитата', 'Цитата', (e) => e.chain().focus().toggleBlockquote().run(), (e) => e.isActive('blockquote')],
    ['Ссылка', 'Ссылка (пусто — убрать)', setLink, (e) => e.isActive('link')],
    ['↶', 'Отменить', (e) => e.chain().focus().undo().run(), null],
    ['↷', 'Повторить', (e) => e.chain().focus().redo().run(), null],
];

function setLink(editor) {
    const previous = editor.getAttributes('link').href ?? 'https://';
    const url = window.prompt('Адрес ссылки (пусто — убрать ссылку)', previous);

    if (url === null) {
        return;
    }

    const chain = editor.chain().focus().extendMarkRange('link');

    url.trim() === '' ? chain.unsetLink().run() : chain.setLink({ href: url.trim() }).run();
}

function mount(textarea) {
    const wrap = document.createElement('div');
    const toolbar = document.createElement('div');
    const area = document.createElement('div');

    wrap.className = 'editor';
    toolbar.className = 'editor-toolbar';
    area.className = 'editor-content';
    wrap.append(toolbar, area);
    textarea.after(wrap);
    textarea.hidden = true;

    const refresh = (editor) => {
        toolbar.querySelectorAll('button').forEach((button, i) => {
            const isActive = buttons[i][3];
            button.classList.toggle('is-active', Boolean(isActive && isActive(editor)));
        });
    };

    const editor = new Editor({
        element: area,
        content: textarea.value,
        extensions: [
            StarterKit.configure({
                code: false,
                codeBlock: false,
                heading: { levels: [2, 3, 4] },
                link: { openOnClick: false, autolink: true, defaultProtocol: 'https' },
            }),
        ],
        onUpdate: ({ editor }) => {
            textarea.value = editor.isEmpty ? '' : editor.getHTML();
        },
        onTransaction: ({ editor }) => refresh(editor),
    });

    buttons.forEach(([label, title, run]) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.textContent = label;
        button.title = title;
        button.setAttribute('aria-label', title);
        button.addEventListener('click', () => run(editor));
        toolbar.append(button);
    });

    refresh(editor);
}

document.querySelectorAll('textarea[data-editor]').forEach(mount);
