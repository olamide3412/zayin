<script setup>
import { ref, onUnmounted } from 'vue';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';
import Link from '@tiptap/extension-link';
import TextAlign from '@tiptap/extension-text-align';
import Image from '@tiptap/extension-image';
import Placeholder from '@tiptap/extension-placeholder';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: 'Start writing your content here...',
    },
});

const emit = defineEmits(['update:modelValue']);

const editor = useEditor({
    content: props.modelValue,
    extensions: [
        StarterKit,
        Underline,
        TextAlign.configure({ types: ['heading', 'paragraph'] }),
        Link.configure({ openOnClick: false }),
        Image,
        Placeholder.configure({ placeholder: props.placeholder }),
    ],
    onUpdate: ({ editor }) => {
        emit('update:modelValue', editor.getHTML());
    },
});

const setLink = () => {
    const url = window.prompt('Enter URL:', editor.value?.getAttributes('link').href);
    if (url === null) return;
    if (url === '') {
        editor.value?.chain().focus().extendMarkRange('link').unsetLink().run();
        return;
    }
    editor.value?.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
};

onUnmounted(() => {
    editor.value?.destroy();
});
</script>

<template>
    <div class="border border-gray-300 rounded-xl overflow-hidden bg-white shadow-sm">
        <!-- Toolbar -->
        <div v-if="editor" class="flex flex-wrap gap-1 p-2 border-b border-gray-200 bg-gray-50">
            <!-- Text Formats -->
            <button type="button" @click="editor.chain().focus().toggleBold().run()" :class="['px-2 py-1 rounded text-sm font-bold transition-colors', editor.isActive('bold') ? 'bg-primary text-white' : 'hover:bg-gray-200 text-gray-700']" title="Bold">B</button>
            <button type="button" @click="editor.chain().focus().toggleItalic().run()" :class="['px-2 py-1 rounded text-sm italic transition-colors', editor.isActive('italic') ? 'bg-primary text-white' : 'hover:bg-gray-200 text-gray-700']" title="Italic">I</button>
            <button type="button" @click="editor.chain().focus().toggleUnderline().run()" :class="['px-2 py-1 rounded text-sm underline transition-colors', editor.isActive('underline') ? 'bg-primary text-white' : 'hover:bg-gray-200 text-gray-700']" title="Underline">U</button>
            <button type="button" @click="editor.chain().focus().toggleStrike().run()" :class="['px-2 py-1 rounded text-sm line-through transition-colors', editor.isActive('strike') ? 'bg-primary text-white' : 'hover:bg-gray-200 text-gray-700']" title="Strikethrough">S</button>

            <div class="w-px h-6 bg-gray-300 mx-1 self-center"></div>

            <!-- Headings -->
            <button type="button" @click="editor.chain().focus().toggleHeading({ level: 1 }).run()" :class="['px-2 py-1 rounded text-sm font-semibold transition-colors', editor.isActive('heading', { level: 1 }) ? 'bg-primary text-white' : 'hover:bg-gray-200 text-gray-700']" title="Heading 1">H1</button>
            <button type="button" @click="editor.chain().focus().toggleHeading({ level: 2 }).run()" :class="['px-2 py-1 rounded text-sm font-semibold transition-colors', editor.isActive('heading', { level: 2 }) ? 'bg-primary text-white' : 'hover:bg-gray-200 text-gray-700']" title="Heading 2">H2</button>
            <button type="button" @click="editor.chain().focus().toggleHeading({ level: 3 }).run()" :class="['px-2 py-1 rounded text-sm font-semibold transition-colors', editor.isActive('heading', { level: 3 }) ? 'bg-primary text-white' : 'hover:bg-gray-200 text-gray-700']" title="Heading 3">H3</button>

            <div class="w-px h-6 bg-gray-300 mx-1 self-center"></div>

            <!-- Lists -->
            <button type="button" @click="editor.chain().focus().toggleBulletList().run()" :class="['px-2 py-1 rounded text-sm transition-colors', editor.isActive('bulletList') ? 'bg-primary text-white' : 'hover:bg-gray-200 text-gray-700']" title="Bullet List">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <button type="button" @click="editor.chain().focus().toggleOrderedList().run()" :class="['px-2 py-1 rounded text-sm transition-colors', editor.isActive('orderedList') ? 'bg-primary text-white' : 'hover:bg-gray-200 text-gray-700']" title="Ordered List">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
            </button>
            <button type="button" @click="editor.chain().focus().toggleBlockquote().run()" :class="['px-2 py-1 rounded text-sm transition-colors', editor.isActive('blockquote') ? 'bg-primary text-white' : 'hover:bg-gray-200 text-gray-700']" title="Blockquote">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
            </button>

            <div class="w-px h-6 bg-gray-300 mx-1 self-center"></div>

            <!-- Alignment -->
            <button type="button" @click="editor.chain().focus().setTextAlign('left').run()" :class="['px-2 py-1 rounded text-sm transition-colors', editor.isActive({ textAlign: 'left' }) ? 'bg-primary text-white' : 'hover:bg-gray-200 text-gray-700']" title="Align Left">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><line x1="17" y1="10" x2="3" y2="10" stroke-linecap="round"/><line x1="21" y1="6" x2="3" y2="6" stroke-linecap="round"/><line x1="21" y1="14" x2="3" y2="14" stroke-linecap="round"/><line x1="17" y1="18" x2="3" y2="18" stroke-linecap="round"/></svg>
            </button>
            <button type="button" @click="editor.chain().focus().setTextAlign('center').run()" :class="['px-2 py-1 rounded text-sm transition-colors', editor.isActive({ textAlign: 'center' }) ? 'bg-primary text-white' : 'hover:bg-gray-200 text-gray-700']" title="Center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><line x1="18" y1="10" x2="6" y2="10" stroke-linecap="round"/><line x1="21" y1="6" x2="3" y2="6" stroke-linecap="round"/><line x1="21" y1="14" x2="3" y2="14" stroke-linecap="round"/><line x1="18" y1="18" x2="6" y2="18" stroke-linecap="round"/></svg>
            </button>

            <div class="w-px h-6 bg-gray-300 mx-1 self-center"></div>

            <!-- Link -->
            <button type="button" @click="setLink" :class="['px-2 py-1 rounded text-sm transition-colors', editor.isActive('link') ? 'bg-primary text-white' : 'hover:bg-gray-200 text-gray-700']" title="Add Link">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            </button>

            <!-- Divider -->
            <button type="button" @click="editor.chain().focus().setHorizontalRule().run()" class="px-2 py-1 rounded text-sm hover:bg-gray-200 text-gray-700 transition-colors" title="Horizontal Rule">—</button>

            <!-- Undo / Redo -->
            <div class="w-px h-6 bg-gray-300 mx-1 self-center"></div>
            <button type="button" @click="editor.chain().focus().undo().run()" :disabled="!editor.can().undo()" class="px-2 py-1 rounded text-sm hover:bg-gray-200 text-gray-700 disabled:opacity-40 transition-colors" title="Undo">↩</button>
            <button type="button" @click="editor.chain().focus().redo().run()" :disabled="!editor.can().redo()" class="px-2 py-1 rounded text-sm hover:bg-gray-200 text-gray-700 disabled:opacity-40 transition-colors" title="Redo">↪</button>
        </div>

        <!-- Editor Content -->
        <editor-content :editor="editor" class="prose prose-primary max-w-none min-h-[300px] p-4 focus:outline-none text-gray-900" />
    </div>
</template>

<style>
/* Tiptap editor placeholder styling */
.tiptap p.is-editor-empty:first-child::before {
    content: attr(data-placeholder);
    float: left;
    color: #adb5bd;
    pointer-events: none;
    height: 0;
}
.tiptap:focus {
    outline: none;
}
.tiptap p { margin-bottom: 0.75rem; }
.tiptap h1 { font-size: 2rem; font-weight: 700; margin-bottom: 1rem; }
.tiptap h2 { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.75rem; }
.tiptap h3 { font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem; }
.tiptap ul { list-style: disc; padding-left: 1.5rem; margin-bottom: 0.75rem; }
.tiptap ol { list-style: decimal; padding-left: 1.5rem; margin-bottom: 0.75rem; }
.tiptap blockquote { border-left: 4px solid #0A96EB; padding-left: 1rem; color: #6b7280; margin: 1rem 0; }
.tiptap a { color: #0A96EB; text-decoration: underline; }
.tiptap hr { border-color: #e5e7eb; margin: 1.5rem 0; }
</style>
