# 任务8：网站内容文件结构

## 一、站点地图（URL 层级）

```
bluestar.com (root)
├── /                          → 首页 (02-homepage.md)
├── /services                  → 服务页 (01-service-page.md)
├── /about                     → 公司介绍 (03-company.md)
├── /careers                   → 岗位体系 (04-positions.md)
├── /cases                     → 案例页 (05-cases.md)
├── /cases/cloud-provider      → 案例一详情
├── /cases/ai-company          → 案例二详情
├── /cases/finance-idc         → 案例三详情
├── /contact                   → 联系/咨询表单页面
├── /privacy                   → 隐私政策
└── /terms                     → 服务条款

日文版（/ja 子目录下）
ja.bluestar.com 或 bluestar.com/ja/
├── /ja/                       → 首页 日文版
├── /ja/services               → 服务页 日文版
├── /ja/about                  → 公司介绍 日文版
├── /ja/careers                → 岗位体系 日文版
├── /ja/cases                  → 案例页 日文版
├── /ja/cases/cloud-provider   → 案例一详情 日文版
├── /ja/cases/ai-company       → 案例二详情 日文版
├── /ja/cases/finance-idc      → 案例三详情 日文版
├── /ja/contact                → 联系/咨询表单 日文版
├── /ja/privacy                → 隐私政策 日文版
└── /ja/terms                  → 服务条款 日文版
```

**推荐方案**：采用 `/ja/` 子目录方式，利于SEO（Google推荐多语种最佳实践），通过hreflang标签进行语言关联。

---

## 二、每个页面的内容组件清单

### 首页组件清单

| 序号 | 组件名称 | 类型 | 内容来源 | 备注 |
|---|---|---|---|---|
| 1 | HeroBanner | 静态区块 | 02-homepage.md | 主标题+副标题+CTA×2 |
| 2 | TrustNumbers | 动态数字滚动 | 02-homepage.md | 4项数据，滚动到视窗触发动画 |
| 3 | ValueProps | 3列卡片 | 02-homepage.md | 核心差异化，每列含序号+标题+正文 |
| 4 | ServiceHighlights | 6宫格图标区 | 02-homepage.md | 6个服务亮点，图标+标题+一句话 |
| 5 | FeaturedCases | 3列案例卡片 | 02-homepage.md | 精选案例，含跳转链接 |
| 6 | SimplifiedProcess | 时间轴/步骤条 | 02-homepage.md | 5步骤水平排列 |
| 7 | ClientLogos | Logo墙 | 占位 | 2行×5列灰色logo |
| 8 | BottomCTA | 静态区块 | 02-homepage.md | 底部转换CTA |

### 服务页组件清单

| 序号 | 组件名称 | 类型 | 内容来源 | 备注 |
|---|---|---|---|---|
| 1 | HeroBanner | 静态区块 | 01-service-page.md | 主标题+副标题+CTA |
| 2 | TrustNumbers | 静态数字条 | 01-service-page.md | 4项数据 |
| 3 | ServiceTabs | Tab切换组件 | 01-service-page.md | 三大模块Tab切换器 |
| 4 | ModuleTables ×3 | 表格列表 | 01-service-page.md | 每个模块6-7个子项表格 |
| 5 | PMSystem | 图文区块 | 01-service-page.md | 项目管理体系7项 |
| 6 | ProcessTimeline | 可视化流程 | 01-service-page.md | 7阶段流程，含交付物 |
| 7 | CaseWall | 案例网格 | 占位 | 2×2网格，跳转案例页 |
| 8 | FAQAccordion | 手风琴组件 | 01-service-page.md | 8组问答 |
| 9 | BottomCTA | 静态区块 | 01-service-page.md | 底部CTA |

### 公司介绍页组件清单

| 序号 | 组件名称 | 类型 | 内容来源 | 备注 |
|---|---|---|---|---|
| 1 | MissionBlock | 静态区块 | 03-company.md | 使命+愿景 |
| 2 | ValuesCards | 卡片列表 | 03-company.md | 6条价值观，含图标+标题+50字阐述 |
| 3 | OrgChart | SVG/图像 | 03-company.md | 组织结构图 |
| 4 | Timeline | 时间轴组件 | 03-company.md | 2018-2025逐年展开 |
| 5 | CertificationTable | 表格 | 03-company.md | 13项认证，含图标 |
| 6 | TeamMembers | 团队成员卡片 | 占位 | 4个成员，照片+姓名+职位+简介 |

### 岗位体系页组件清单

| 序号 | 组件名称 | 类型 | 内容来源 | 备注 |
|---|---|---|---|---|
| 1 | DepartmentTabs | Tab切换 | 04-positions.md | 3个部门Tab切换 |
| 2 | PositionCards ×10 | 卡片折叠 | 04-positions.md | 每个岗位可展开详情 |
| 3 | CareerPathChart | 可视化路径 | 04-positions.md | 每个岗位下方水平路径 |
| 4 | BottomCTA | 静态区块 | — | "加入我们"招聘入口 |

### 案例页组件清单

| 序号 | 组件名称 | 类型 | 内容来源 | 备注 |
|---|---|---|---|---|
| 1 | CaseFilter | 筛选栏 | — | 按客户类型/服务类型筛选 |
| 2 | CaseCards ×N | 卡片网格 | 05-cases.md | 每个案例一张卡片 |
| 3 | CaseDetailModal | 模态详情 | 05-cases.md | 点击展开完整案例内容 |

---

## 三、图片/图标/视频需求清单

### 图片需求

| 编号 | 图片用途 | 规格建议 | 数量 | 优先级 |
|---|---|---|---|---|
| IMG-01 | 首页Hero背景 | 1920×1080px, WebP | 1 | P0 |
| IMG-02 | 服务页Hero背景 | 1920×1080px, WebP | 1 | P0 |
| IMG-03 | 公司介绍页Hero背景 | 1920×1080px, WebP | 1 | P0 |
| IMG-04 | 案例一-机房全景 | 1920×1080px, WebP | 1 | P0 |
| IMG-05 | 案例一-验收现场 | 1200×800px, WebP | 1 | P0 |
| IMG-06 | 案例一-团队合影 | 1200×800px, WebP | 1 | P0 |
| IMG-07 | 案例二-基建施工中 | 1920×1080px, WebP | 1 | P0 |
| IMG-08 | 案例二-冷通道全景 | 1920×1080px, WebP | 1 | P0 |
| IMG-09 | 案例二-运维团队照 | 1200×800px, WebP | 1 | P0 |
| IMG-10 | 案例三-消防系统 | 1200×800px, WebP | 1 | P0 |
| IMG-11 | 案例三-运维工位 | 1200×800px, WebP | 1 | P0 |
| IMG-12 | 案例三-动环监控大屏 | 1920×1080px, WebP | 1 | P0 |
| IMG-13 | 团队成员照片×4 | 400×500px, WebP | 4 | P1 |
| IMG-14 | 首页精选案例缩略图×3 | 600×400px, WebP | 3 | P0 |
| IMG-15 | 组织架构图 | SVG优先/PNG后备 | 1 | P0 |

### 图标需求

| 编号 | 图标用途 | 格式 | 数量 | 优先级 |
|---|---|---|---|---|
| ICO-01 | 服务亮点6宫格图标 | SVG | 6 | P0 |
| ICO-02 | 价值主张3图标 | SVG | 3 | P0 |
| ICO-03 | 项目管理体系7图标 | SVG | 7 | P0 |
| ICO-04 | 合作流程7步骤图标 | SVG | 7 | P0 |
| ICO-05 | 公司价值观6图标 | SVG | 6 | P0 |
| ICO-06 | 3部门Tab切换图标 | SVG | 3 | P0 |
| ICO-07 | 认证图标（13项） | SVG | 13 | P1 |
| ICO-08 | 底部CTA背景图案 | SVG | 1 | P1 |
| ICO-09 | 服务子项分类图标 | SVG | 18 | P1 |
| ICO-10 | 社交图标（微信/LinkedIn/邮箱） | SVG | 3 | P1 |
| ICO-11 | 导航栏Logo（中文/日文版） | SVG | 1 | P0 |

### 视频需求

| 编号 | 视频用途 | 规格建议 | 时长 | 优先级 |
|---|---|---|---|---|
| VID-01 | 首页Hero背景视频（可选，替代静态图） | 1920×1080, MP4/H.264, ≤8MB | 15-30秒 | P2 |
| VID-02 | 服务介绍短视频（可选） | 1920×1080, MP4, ≤20MB | 60-90秒 | P2 |
| VID-03 | 客户证言视频（可选） | 1920×1080, MP4 | 30-60秒/个 | P2 |

---

## 四、特殊功能需求

### 功能清单

| 序号 | 功能名称 | 页面 | 描述 | 技术实现建议 |
|---|---|---|---|---|
| F-01 | 数字滚动动画 | 首页 / 服务页 | 信任数据从0滚动到最终值 | CSS animation + Intersection Observer |
| F-02 | FAQ手风琴 | 服务页 | 点击问号展开/收起答案 | 原生JS或React state toggle |
| F-03 | 案例筛选 | 案例页 | 按"客户类型"和"服务类型"筛选案例 | 前端filter，无需后端API |
| F-04 | 服务模块Tab切换 | 服务页 | 三大服务模块切换显示 | Tab组件，切换动画（fade/slide） |
| F-05 | 岗位部门Tab切换 | 岗位体系页 | 三大部门切换显示 | Tab组件，同F-04 |
| F-06 | 岗位卡片折叠展开 | 岗位体系页 | 每个岗位详情可展开/收起 | Accordion组件 |
| F-07 | 发展历程时间轴动画 | 公司介绍页 | 逐年展开，滚动触发 | Intersection Observer + CSS |
| F-08 | 案例详情模态框 | 案例页 | 点击案例卡片弹出完整信息 | Modal/Dialog组件 |
| F-09 | 语言切换 | 全部页面 | 中文/日文一键切换 | /ja/ 子目录 + hreflang标签 |
| F-10 | 响应式设计 | 全部页面 | 桌面/平板/手机三端适配 | CSS Grid/Flexbox |
| F-11 | 导航栏粘性定位 | 全部页面 | 滚动时导航栏固定在顶部 | CSS position: sticky |
| F-12 | 底栏CTA固定 | 全部页面 | 底部CTA区域含表单或按钮 | 静态区块 |
| F-13 | 客户Logo懒加载 | 首页 | Logo图片延迟加载以提高首屏性能 | Lazy loading + placeholder |
| F-14 | 图片懒加载 | 全部页面 | 非首屏图片延迟加载 | loading="lazy"属性 |
| F-15 | 平滑滚动 | 全部页面 | 锚点跳转时的平滑滚动效果 | CSS scroll-behavior: smooth |
| F-16 | 面包屑导航 | 内容页 | 显示当前位置层级 | 根据当前URL自动生成 |
| F-17 | 联系表单 | 联系页 | 姓名/公司/邮箱/电话/需求描述 | 表单验证+邮件发送API |
| F-18 | 服务子项筛选（可选项） | 服务页 | 按关键词筛选服务子项 | 前端filter |
| F-19 | 一键返回顶部 | 全部页面 | 长页面右下角浮动按钮 | Intersection Observer + scroll |

### 性能要求

- 首屏加载时间 < 2秒（桌面）/ < 3秒（移动端）
- Lighthouse 性能得分 ≥ 85
- 图片使用 WebP 格式（备选 JPEG/PNG for older browsers）
- 使用 CDN 部署静态资源
- 启用 Gzip/Brotli 压缩

### SEO 技术要求

- 每个页面必含：Title / Description / Open Graph / Twitter Card
- hreflang 标签关联中/日版本
- 结构化数据（Schema.org）：Organization, LocalBusiness, Product（服务）
- 生成 sitemap.xml
- 生成 robots.txt
- SSR 或 SSG 保证搜索引擎可爬取（推荐：Next.js SSG 或 Nuxt 3 SSG）

---

## 五、CMS 内容模型建议

### 内容类型：Page（页面）

| 字段名 | 类型 | 说明 | 必填 |
|---|---|---|---|
| title | String | 页面标题 | ✓ |
| slug | String | URL路径 | ✓ |
| locale | String | 语言：zh / ja | ✓ |
| localeParent | Reference | 关联的翻译页 | — |
| metaTitle | String | SEO Title | ✓ |
| metaDescription | Text | Meta Description | ✓ |
| metaKeywords | String | Meta Keywords | — |
| components | JSON/Blocks | 页面组件数据 | ✓ |
| publishedAt | DateTime | 发布时间 | ✓ |
| status | Enum | draft / published / archived | ✓ |

### 内容类型：Global Settings（全局设置）

| 字段名 | 类型 | 说明 | 必填 |
|---|---|---|---|
| siteName | String | 站点名称 | ✓ |
| logo | Image | Logo（SVG） | ✓ |
| navigation | JSON | 导航栏结构 | ✓ |
| footer | JSON | 页脚信息 | ✓ |
| contactInfo | JSON | 联系信息 | ✓ |
| socialLinks | JSON | 社交媒体链接 | — |
| gaId | String | Google Analytics ID | — |
| locale | String | 语言 | ✓ |

### 内容类型：Case Study（案例）

| 字段名 | 类型 | 说明 | 必填 |
|---|---|---|---|
| title | String | 案例标题 | ✓ |
| slug | String | URL路径 | ✓ |
| locale | String | 语言 | ✓ |
| clientType | Enum | 云厂商/AI/金融/互联网 | ✓ |
| serviceType | Multiple Enum | 基建/交付/运维 | ✓ |
| summary | String | 一句话简介 | ✓ |
| coverImage | Image | 案例缩略图 | ✓ |
| content | Rich Text / Blocks | 案例完整内容 | ✓ |
| relatedServices | Multiple Relation | 关联服务 | — |
| clientTestimonial | Text | 客户证言 | — |
| photos | Multiple Image | 项目照片 | — |
| publishedAt | DateTime | — | ✓ |
| featured | Boolean | 是否首页精选 | — |
| localeParent | Reference | 关联翻译案例 | — |

### 内容类型：Service Module（服务模块）

| 字段名 | 类型 | 说明 | 必填 |
|---|---|---|---|
| title | String | 模块名称 | ✓ |
| order | Number | 排序 | ✓ |
| locale | String | 语言 | ✓ |
| icon | Image | 图标 | ✓ |
| summary | String | 一句话简介 | ✓ |
| subServices | JSON | 子项列表（名称+说明） | ✓ |
| localeParent | Reference | 关联翻译 | — |

### 内容类型：Job Position（岗位）

| 字段名 | 类型 | 说明 | 必填 |
|---|---|---|---|
| title | String | 岗位名称 | ✓ |
| department | Enum | 商务/交付/运维 | ✓ |
| locale | String | 语言 | ✓ |
| summary | String | 一句话介绍 | ✓ |
| responsibilities | Array(Text) | 职责列表 | ✓ |
| requirements | Array(Text) | 任职要求 | ✓ |
| careerPath | JSON | 发展路径 | ✓ |
| localeParent | Reference | 关联翻译 | — |
| isOpen | Boolean | 是否在招 | ✓ |

### 内容类型：FAQ Item（FAQ）

| 字段名 | 类型 | 说明 | 必填 |
|---|---|---|---|
| question | String | 问题 | ✓ |
| answer | Rich Text | 回答 | ✓ |
| order | Number | 排序 | ✓ |
| locale | String | 语言 | ✓ |
| relatedPage | Reference | 所属页面 | ✓ |
| localeParent | Reference | 关联翻译 | — |

---

## 六、前端项目目录结构（推荐）

```
bluestar-website/
├── public/
│   ├── favicon.ico
│   ├── robots.txt
│   ├── sitemap.xml
│   ├── images/
│   │   ├── hero/              # 各页面Hero背景
│   │   ├── cases/             # 案例图片
│   │   ├── team/              # 团队成员照片
│   │   ├── logos/             # 客户Logo
│   │   └── icons/             # SVG图标
│   └── videos/                # 视频资源
│
├── src/
│   ├── components/
│   │   ├── common/
│   │   │   ├── Header.tsx
│   │   │   ├── Footer.tsx
│   │   │   ├── Breadcrumb.tsx
│   │   │   ├── CTASection.tsx
│   │   │   ├── HeroBanner.tsx
│   │   │   ├── TrustNumbers.tsx (滚动动画)
│   │   │   ├── ClientLogos.tsx
│   │   │   ├── FAQAccordion.tsx
│   │   │   ├── TabSwitcher.tsx
│   │   │   ├── BackToTop.tsx
│   │   │   └── LanguageSwitch.tsx
│   │   ├── home/
│   │   │   ├── ValueProps.tsx
│   │   │   ├── ServiceHighlights.tsx
│   │   │   ├── FeaturedCases.tsx
│   │   │   └── SimplifiedProcess.tsx
│   │   ├── services/
│   │   │   ├── ModuleTable.tsx
│   │   │   ├── PMSystem.tsx
│   │   │   └── ProcessTimeline.tsx
│   │   ├── about/
│   │   │   ├── MissionVision.tsx
│   │   │   ├── ValuesCards.tsx
│   │   │   ├── OrgChart.tsx
│   │   │   ├── Timeline.tsx
│   │   │   ├── CertificationTable.tsx
│   │   │   └── TeamMembers.tsx
│   │   ├── careers/
│   │   │   ├── PositionCard.tsx
│   │   │   └── CareerPath.tsx
│   │   └── cases/
│   │       ├── CaseFilter.tsx
│   │       ├── CaseCard.tsx
│   │       └── CaseDetailModal.tsx
│   │
│   ├── pages/
│   │   ├── index.tsx           # 首页
│   │   ├── services.tsx        # 服务页
│   │   ├── about.tsx           # 公司介绍
│   │   ├── careers.tsx         # 岗位体系
│   │   ├── cases/
│   │   │   ├── index.tsx       # 案例列表
│   │   │   ├── [slug].tsx      # 案例详情
│   │   │   └── ...
│   │   ├── contact.tsx         # 联系页
│   │   ├── ja/
│   │   │   ├── index.tsx       # 日文首页
│   │   │   ├── services.tsx    # 日文服务页
│   │   │   ├── about.tsx       # 日文公司介绍
│   │   │   ├── careers.tsx     # 日文岗位体系
│   │   │   ├── cases/
│   │   │   │   ├── index.tsx   # 日文案例列表
│   │   │   │   └── [slug].tsx  # 日文案例详情
│   │   │   └── ...
│   │   └── _app.tsx
│   │
│   ├── lib/
│   │   ├── i18n.ts                    # 国际化配置
│   │   ├── seo.ts                     # SEO常量
│   │   └── constants.ts               # 站点常量
│   │
│   ├── styles/
│   │   ├── globals.css
│   │   ├── animations.css
│   │   └── components/
│   │
│   ├── types/
│   │   ├── page.ts
│   │   ├── case.ts
│   │   ├── service.ts
│   │   ├── position.ts
│   │   └── faq.ts
│   │
│   └── data/                # CMS数据文件/API
│       ├── home-zh.json
│       ├── home-ja.json
│       ├── services-zh.json
│       ├── services-ja.json
│       └── ...
│
├── package.json
├── tsconfig.json
├── next.config.js (or nuxt.config.ts)
└── .env.local
```

---

