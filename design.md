# ToolBox · 设计系统（锁定）

> 本文件由 Hallmark 多页重构流程生成，是整个站点的**唯一设计事实源**。
> 后续新增页面必须沿用本系统，不再逐页挑主题。改动本文件 = 改全站。

## 定位

工具匣 ToolBox 是一个纯前端、数据不出浏览器的在线工具站。设计目标是「像一本排得讲究的工具索引」，而不是 SaaS 落地页。读者来这里是为了**找到并开始用**，不是为了看营销。

- 语气：实用型编辑感（utilitarian-editorial），克制、具体、不吆喝。
- 受众：中文互联网用户，桌面与手机各半。
- 反目标：紫色渐变、玻璃拟态、居中英雄区、卡片套卡片、AI 式营销文案。

## 设计坐标

| 维度 | 取值 |
| --- | --- |
| Genre | editorial |
| Macrostructure | Index-First |
| Theme | Almanac（暖燕麦纸 + 印章朱红） |
| Nav | N13 内联搜索胶囊（词标 · 4 链接 · 搜索 · 主题） |
| Footer | Ft4 密集版权页（colophon） |
| Enrichment | none |
| Motion | 克制：仅 hover/focus 微反馈 + 主题切换 300ms |

## 排版

2+1 字体纪律：

- `--font-display`：Noto Serif SC 600/700 —— 只用于 h1、h2、索引分类标题、页脚刊头句。
- `--font-body`：Noto Sans SC 400/500 —— 正文、按钮、导航。
- `--font-outlier`：JetBrains Mono 400/700 —— 编号、日期、分类计数、面包屑、代码、表头小标签。

规则：

- 标题一律正体（`font-style: normal`），强调用字重或印章红，不用斜体。
- 字号走 `--text-*` 阶梯；超大标题用 `--text-display` 的 `clamp()`。
- 中文正文行高 ≥ 1.7；阅读段落宽度 ≤ 68ch。

## 色彩（OKLCH，浅/深双套）

- 纸面：`--color-paper` / `--color-paper-2`（暖调，非纯白）。
- 墨色：`--color-ink`（近黑非纯黑）、`--color-ink-2`（正文）、`--color-ink-3`（次要/元信息）。
- 分隔：`--color-rule` / `--color-rule-strong`（发丝线）。
- 唯一强调色：`--color-accent`（印章朱红 30°），`--color-accent-deep`（按下态）、`--color-accent-soft`（选中底色）、`--color-accent-ink`（红底上的字）。
- 焦点环：`--color-focus`。

约定：

- 强调色占画面 ≤ 5%；其余靠墨色层级和发丝线建立秩序。
- 禁止渐变、纯黑 `#000`、纯白 `#fff`、发光阴影、彩色光晕。
- 任何颜色/字体必须走 token，禁止在模板或页面里写死 hex / OKLCH / 字体名。

## 间距与形状

- 4pt 基准的 `--space-3xs … --space-4xl`，不即兴造值。
- 圆角：`--radius-sm`（代码、标签）、`--radius-md`（输入、按钮）、`--radius-pill`（搜索胶囊）。
- 边框统一 `--rule-hair`（1px）发丝线；索引分类头用 2px 墨色下划线做层级。

## 组件约定

- **索引行** `.idx-row`：名称 / 描述 / 箭头三列。悬停时名称变红、箭头淡入；触屏设备箭头常显。博客索引加日期列（`.idx-row--post` + `.idx-date`）。
- **页头** `.page-head`：h1 + 一句副标题 + 可选 `.page-meta`（等宽小字）。**不使用眉标（eyebrow/kicker）**。
- **面包屑** `.tb-breadcrumb`：等宽小字，`aria-current="page"` 标当前页。
- **按钮** `.btn`：墨色描边、透明底；悬停转为印章红底纸字；按下位移 1px。
- **工具外壳** `.tool-shell`：输入、按钮、消息、结果框的统一容器；25 个工具页只换皮，不改功能逻辑。
- **编辑页** `.prose`：衬线小标题 + 无衬线正文；代码块用纸面色底而非暗色块。
- **404** `.error-page`：等宽朱红 404 + 本地搜索 + 入口按钮。

## 交互与可达性

- 全站搜索：⌘K / Ctrl+K 或 `/` 聚焦；组合框语义（`role="combobox"`、`role="listbox"`、`aria-activedescendant`）；上下键选择、回车跳转、Esc 关闭；结果数用 `aria-live="polite"` 播报。
- 焦点环即时出现（不淡入），2px、≥3:1 对比。
- 所有悬停反馈都有键盘等价物；触屏下箭头常显。
- `prefers-reduced-motion: reduce` 时关闭空间动画。
- 移动端（≤720px）：导航收进菜单、搜索独占一行、索引行改为「标题+日期 / 描述」两行。
- 根元素 `overflow-x: clip`；图片列用 `minmax(0, 1fr)`；可点文字不换行。

## 文案

- 具体动词、可独立成立的链接文字；不用「点击这里」「Oops」「解锁你的…」「赋能」。
- 不编造数据：没有真实指标就用占位或换结构。
- 标点用中文全角与正确破折号 / 省略号。

## 文件位置

- 令牌：`public/css/tokens.css`（源）与项目根 `tokens.css`（便携副本）。
- 样式：`public/css/app.css`（唯一全局样式表）。
- 布局：`resources/views/layouts/app.blade.php`。
- 每页通过 `@section('description', …)` 提供**唯一** meta description（不要再写 `@section('meta')` 里的 description）。
